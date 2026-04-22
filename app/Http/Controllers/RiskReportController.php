<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskItem;
use App\Models\RiskReport;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class RiskReportController extends Controller
{
    public function create($kategori)
    {
        // Validasi biar orang gak ngetik URL sembarangan
        if (!in_array($kategori, ['finansial', 'non-finansial'])) {
            abort(404, 'Kategori Risiko Tidak Ditemukan');
        }

        $userRole = Auth::user()->roles->first()->name;

        // Tarik soal yang sesuai dengan JABATAN dan KATEGORI-nya
        $riskItems = RiskItem::with('causes.mitigations')
            ->where('role_target', $userRole)
            ->where('kategori', $kategori)
            ->get();

        return view('risk_reports.create', compact('riskItems', 'kategori'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Dinamis (Tergantung Kategori)
        $rules = [
            'kategori' => 'required|in:finansial,non-finansial',
            'tanggal_kejadian' => 'required|date',
            'tanggal_diketahui' => 'required|date',
            'risk_item_id' => 'required',
            'risk_cause_id' => 'required_without:other_item_description',
            'other_item_description' => 'required_without:risk_cause_id',
        ];

        // Kalau Finansial, wajib isi nominal angka
        if ($request->kategori === 'finansial') {
            $rules['dampak_finansial'] = 'required|numeric';
        }
        // Kalau Non-Finansial, wajib isi keterangan cerita
        else {
            $rules['dampak_non_finansial'] = 'required|string';
        }
        $rules['tindakan_awal'] = 'nullable|string';
        $rules['status_awal'] = 'required|in:open,in_progress';

        // Cukup panggil validasi SATU KALI aja
        $request->validate($rules);

        $user = Auth::user();
        $targetApproval = $user->hasRole('kacab') ? 'pending_korwil' : 'pending_kacab';

        // 2. Simpan ke database (TANGKAP HASILNYA KE DALAM VARIABEL $report)
        $report = RiskReport::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'kategori' => $request->kategori,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'tanggal_diketahui' => $request->tanggal_diketahui,
            'risk_item_id' => $request->risk_item_id,
            'other_item_description' => $request->other_item_description,
            'risk_cause_id' => $request->risk_cause_id,
            'other_cause_description' => $request->other_cause_description,
            'mitigasi_tambahan' => $request->mitigasi_tambahan,
            'dampak_finansial' => $request->dampak_finansial ?? 0,
            'dampak_non_finansial' => $request->dampak_non_finansial,
            'skala_dampak' => $request->skala_dampak,
            'approval_status' => $targetApproval,
            'resolution_status' => $request->status_awal,
        ]);

        // 3. Otomatis Bikin Log Progress Pertama (Kalau Maker ngisi tindakan awal)
        if ($request->filled('tindakan_awal')) {
            $report->logs()->create([
                'user_id' => $user->id,
                'note' => 'Penanganan Awal: ' . $request->tindakan_awal,
                'status_after_note' => $request->status_awal
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim!');
    }

    // VIEW 1 & 2: MONITORING & PERSETUJUAN
    // FUNGSI KHUSUS CHECKER (KACAB/KORWIL)
    public function review()
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $reports = collect();
        $tindakLanjut = collect();

        if ($role === 'kacab') {
            // 1. Tabel Persetujuan (Menunggu Approval Kacab)
            $reports = RiskReport::with(['user', 'item', 'cause.mitigations'])
                ->where('branch_id', $user->branch_id)
                ->where('approval_status', 'pending_kacab')
                ->get();

            // 2. Tabel Tindak Lanjut (Kacab wajib pantau SEMUA laporan di cabangnya)
            $tindakLanjut = RiskReport::with(['user', 'item', 'cause.mitigations'])
                ->where('branch_id', $user->branch_id)
                ->where('approval_status', 'approved')
                ->whereIn('resolution_status', ['open', 'in_progress'])
                ->orderBy('updated_at', 'desc')
                ->get();

        } elseif ($role === 'korwil') {
            $branchIds = \App\Models\Branch::where('korwil_id', $user->id)->pluck('id');

            // 1. Tabel Persetujuan (Menunggu Approval Korwil)
            $reports = RiskReport::with(['user', 'item', 'cause.mitigations'])
                ->whereIn('branch_id', $branchIds)
                ->where('approval_status', 'pending_korwil')
                ->get();

            // 2. Tabel Tindak Lanjut (Korwil HANYA pantau laporan milik Kacab)
            $tindakLanjut = RiskReport::with(['user', 'item', 'cause.mitigations'])
                ->whereIn('branch_id', $branchIds)
                ->where('approval_status', 'approved')
                ->whereIn('resolution_status', ['open', 'in_progress'])
                // INI GEMBOK SAKTINYA: Filter berdasarkan role_target di tabel master
                ->whereHas('item', function($q) {
                    $q->where('role_target', 'kacab');
                })
                ->orderBy('updated_at', 'desc')
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
    // VIEW 3: RIWAYAT & MONITORING KESELURUHAN (DENGAN FILTER)
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;

        // 1. Tarik Relasi Dasar
        $query = RiskReport::with(['user', 'item', 'cause.mitigations', 'branch']);

        // 2. Filter Otoritas (Tembok Keamanan)
        // ... (Kodingan relasi atasnya tetep sama)

        // 2. Filter Otoritas (Tembok Keamanan)
        if ($role === 'kacab') {
            $query->where('branch_id', $user->branch_id);
            $branches = collect();
        } elseif ($role === 'korwil') {
            $branchIds = \App\Models\Branch::where('korwil_id', $user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
            $branches = \App\Models\Branch::whereIn('id', $branchIds)->get();
        } elseif (in_array($role, ['teller', 'ca', 'csr', 'security'])) {
            // MAKER CUMA BISA LIAT LAPORAN DIA SENDIRI
            $query->where('user_id', $user->id);
            $branches = collect(); // Maker nggak butuh filter dropdown cabang
        } else {
            // ManRisk bisa liat semua
            $branches = \App\Models\Branch::all();
        }

        // ... (Sisa kodingan filter bawahnya biarin tetep sama)

        // 3. Eksekusi Filter Dinamis (Form GET)

        // A. Filter Cabang (Hanya Korwil & ManRisk)
        if ($request->filled('branch_id') && in_array($role, ['manrisk', 'korwil'])) {
            $query->where('branch_id', $request->branch_id);
        }

        // B. Filter Kategori Risiko (BARU)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // C. Filter Jabatan (BARU)
        if ($request->filled('jabatan')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('role_target', $request->jabatan);
            });
        }

        // D. Filter Rentang Waktu
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_kejadian', [$request->start_date, $request->end_date]);
        }

        // ... (Filter cabang, kategori, dll)

        // E. Filter Status Penyelesaian (BARU)
        if ($request->filled('resolution_status')) {
            $query->where('resolution_status', $request->resolution_status);
        }

        // ... (Filter Tanggal dan eksekusi get())

        // 4. Hitung Metrik Dashboard LANGSUNG DI DATABASE (Nggak makan RAM)
        // Kita clone query-nya biar nggak ngerusak query utama yang mau di-get()
        $totalLoss = (clone $query)->where('approval_status', 'approved')->sum('dampak_finansial');
        $totalKejadian = (clone $query)->count();
        $totalRejected = (clone $query)->where('approval_status', 'rejected')->count();

        // 5. Tarik Datanya
        $reports = $query->orderBy('tanggal_kejadian', 'desc')->get();

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

    // FUNGSI TAMBAHAN: CATAT PROGRESS TINDAK LANJUT (NOTE + STATUS)
    public function addProgress(Request $request, $id)
    {
        $user = Auth::user();

        // GEMBOK BACK-END: ManRisk dilarang keras eksekusi fungsi ini
        if ($user->hasRole('manrisk')) {
            return back()->with('error', 'Akses Ditolak! Divisi ManRisk hanya berwenang memantau, bukan mengubah progress penanganan.');
        }
        $request->validate([
            'note' => 'required|string|min:5',
            'new_status' => 'required|in:in_progress,closed'
        ]);

        $report = RiskReport::findOrFail($id);
        $user = Auth::user();

        // VALIDASI OTORITAS: Cuma Kacab/Korwil yang bisa ngetok status 'closed'
        if ($request->new_status === 'closed' && !$user->hasAnyRole(['kacab', 'korwil'])) {
            return back()->with('error', 'Hanya Atasan yang bisa menutup (Closed) laporan ini.');
        }

        // 1. Simpan Log Catatan
        $report->logs()->create([
            'user_id' => $user->id,
            'note' => $request->note,
            'status_after_note' => $request->new_status
        ]);

        // 2. Update Status Utama Laporan
        $report->update(['resolution_status' => $request->new_status]);

        return back()->with('success', 'Progress berhasil dicatat!');
    }

    // Nampilin Detail Laporan & Timeline
    public function show($id)
    {
        $report = RiskReport::with(['user', 'item', 'branch', 'cause.mitigations', 'logs.user'])->findOrFail($id);

        return view('risk_reports.show', compact('report'));
    }
}
