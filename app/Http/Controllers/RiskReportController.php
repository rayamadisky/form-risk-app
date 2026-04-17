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
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $query = RiskReport::with(['user', 'item', 'cause', 'branch']);

        // Filter Riwayat berdasarkan Jabatan
        if ($role === 'kacab') {
            $query->where('branch_id', $user->branch_id);
        } elseif ($role === 'korwil') {
            $branchIds = Branch::where('korwil_id', $user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        } 
        // ManRisk biarin aja, bakal narik semuanya

        $reports = $query->orderBy('updated_at', 'desc')->get();
        $totalLoss = $reports->where('approval_status', 'approved')->sum('dampak_finansial');

        return view('risk_reports.index', compact('reports', 'totalLoss'));
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