<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskItem;
use App\Models\RiskReport;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class RiskReportController extends Controller
{
    public function create()
    {
        $userRole = Auth::user()->roles->first()->name;
        $riskItems = RiskItem::with('causes.mitigations')
            ->where('role_target', $userRole)
            ->get();

        return view('risk_reports.create', compact('riskItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_kejadian' => 'required|date',
            'tanggal_diketahui' => 'required|date',
            'risk_item_id' => 'required',
            'risk_cause_id' => 'required_without:other_item_description',
            'other_item_description' => 'required_without:risk_cause_id',
            'dampak_finansial' => 'required|numeric',
        ]);

        $user = Auth::user();
        
        // LOGIKA MAKER-CHECKER:
        // Jika yang lapor Kacab, maka status lari ke 'pending_korwil'
        // Jika selain Kacab (Teller/CS/Security/CA), lari ke 'pending_kacab'
        $targetApproval = $user->hasRole('kacab') ? 'pending_korwil' : 'pending_kacab';

        RiskReport::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'tanggal_diketahui' => $request->tanggal_diketahui,
            'risk_item_id' => $request->risk_item_id,
            'other_item_description' => $request->other_item_description,
            'risk_cause_id' => $request->risk_cause_id,
            'other_cause_description' => $request->other_cause_description,
            'mitigasi_tambahan' => $request->mitigasi_tambahan,
            'dampak_finansial' => $request->dampak_finansial,
            'approval_status' => $targetApproval,
            'resolution_status' => 'open', // Default selalu terbuka (belum selesai)
        ]);

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim!');
    }

    // VIEW 1 & 2: MONITORING & PERSETUJUAN
    public function review()
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $query = RiskReport::with(['user', 'item', 'cause']);

        if ($role === 'kacab') {
            // Kacab liat yang pending_kacab di cabangnya sendiri
            $reports = $query->where('branch_id', $user->branch_id)
                             ->where('approval_status', 'pending_kacab')
                             ->get();
            
            // VIEW 2: Laporan yang udah approve tapi BELUM SELESAI (Resolution)
            $tindakLanjut = RiskReport::where('branch_id', $user->branch_id)
                             ->where('approval_status', 'approved')
                             ->whereIn('resolution_status', ['open', 'monitoring'])
                             ->get();

        } elseif ($role === 'korwil') {
            // Korwil liat semua cabang yang ada di bawah ID dia
            $branchIds = Branch::where('korwil_id', $user->id)->pluck('id');
            
            $reports = $query->whereIn('branch_id', $branchIds)
                             ->where('approval_status', 'pending_korwil')
                             ->get();
                             
            $tindakLanjut = RiskReport::whereIn('branch_id', $branchIds)
                             ->where('approval_status', 'approved')
                             ->whereIn('resolution_status', ['open', 'monitoring'])
                             ->get();
        }

        return view('risk_reports.review', compact('reports', 'tindakLanjut'));
    }

    // PROSES APPROVAL
    public function updateStatus(Request $request, $id)
    {
        $report = RiskReport::findOrFail($id);
        $request->validate(['status' => 'required|in:approved,rejected']);

        // Update status persetujuan
        $report->update(['approval_status' => $request->status]);

        return redirect()->back()->with('success', 'Status persetujuan diperbarui!');
    }

    // VIEW 3: RIWAYAT (HISTORY)
    // VIEW 3: RIWAYAT & MONITORING KESELURUHAN (DENGAN FILTER)
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;

        // Tambahin 'cause.mitigations' biar database narik sekalian solusi dari master data
        $query = RiskReport::with(['user', 'item', 'cause.mitigations', 'branch']);

        // 2. Filter Otoritas (Tembok Keamanan)
        if ($role === 'kacab') {
            $query->where('branch_id', $user->branch_id);
            $branches = collect(); // Kacab nggak butuh milih cabang
        } elseif ($role === 'korwil') {
            $branchIds = \App\Models\Branch::where('korwil_id', $user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
            $branches = \App\Models\Branch::whereIn('id', $branchIds)->get(); // Korwil cuma lihat cabangnya
        } else {
            // ManRisk (Dewa) bisa lihat semua cabang
            $branches = \App\Models\Branch::all();
        }

        // 3. Eksekusi Filter Dinamis dari Request (Form GET)
        
        // Filter Cabang (Hanya berlaku untuk ManRisk & Korwil)
        if ($request->filled('branch_id') && in_array($role, ['manrisk', 'korwil'])) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter Rentang Waktu (Berdasarkan tanggal kejadian)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_kejadian', [$request->start_date, $request->end_date]);
        }

        // 4. Tarik Datanya
        $reports = $query->orderBy('tanggal_kejadian', 'desc')->get();
        
        // 5. Hitung Metrik Dashboard
        $totalLoss = $reports->where('approval_status', 'approved')->sum('dampak_finansial');
        $totalKejadian = $reports->count();
        $totalRejected = $reports->where('approval_status', 'rejected')->count();

        return view('risk_reports.index', compact('reports', 'totalLoss', 'totalKejadian', 'totalRejected', 'branches', 'role'));
    }

    // UPDATE TINDAK LANJUT (RESOLUTION)
    public function updateResolution(Request $request, $id)
    {
        $report = RiskReport::findOrFail($id);
        $request->validate(['resolution_status' => 'required|in:monitoring,closed']);

        $report->update(['resolution_status' => $request->resolution_status]);

        return redirect()->back()->with('success', 'Status tindak lanjut diperbarui!');
    }
}