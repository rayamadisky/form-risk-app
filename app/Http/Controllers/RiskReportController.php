<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskItem;
use App\Models\RiskReport;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Requests\StoreRiskReportRequest;
use App\Http\Requests\UpdateRiskApprovalStatusRequest;
use App\Http\Requests\UpdateRiskResolutionRequest;
use App\Http\Requests\AddRiskProgressRequest;

class RiskReportController extends Controller
{
    private function primaryRoleName(): ?string
    {
        return Auth::user()?->primaryRoleName();
    }

    private function ensureCanViewReport(RiskReport $report): void
    {
        $user = Auth::user();
        $role = $user?->primaryRoleName();

        if (!$user || !$role) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
        }

        if ($role === 'manrisk') {
            return;
        }

        if ($role === 'kacab' && (int) $report->branch_id === (int) $user->branch_id) {
            return;
        }

        if ($role === 'korwil') {
            $branchIds = Branch::where('korwil_id', $user->id)->pluck('id');
            if ($branchIds->contains((int) $report->branch_id)) {
                return;
            }
        }

        if (in_array($role, ['teller', 'ca', 'csr', 'security'], true) && (int) $report->user_id === (int) $user->id) {
            return;
        }

        abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
    }

    private function ensureCanApproveReport(RiskReport $report): void
    {
        $user = Auth::user();
        $role = $user?->primaryRoleName();

        if (!$user || !$role) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
        }

        if ($role === 'kacab') {
            if ((int) $report->branch_id !== (int) $user->branch_id || $report->approval_status !== 'pending_kacab') {
                abort(Response::HTTP_FORBIDDEN, 'Anda tidak berwenang menyetujui laporan ini.');
            }
            return;
        }

        if ($role === 'korwil') {
            $branchIds = Branch::where('korwil_id', $user->id)->pluck('id');
            if (!$branchIds->contains((int) $report->branch_id) || $report->approval_status !== 'pending_korwil') {
                abort(Response::HTTP_FORBIDDEN, 'Anda tidak berwenang menyetujui laporan ini.');
            }
            return;
        }

        abort(Response::HTTP_FORBIDDEN, 'Anda tidak berwenang menyetujui laporan ini.');
    }

    private function ensureCanUpdateProgress(RiskReport $report): void
    {
        $user = Auth::user();
        $role = $user?->primaryRoleName();

        if (!$user || !$role) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
        }

        // ManRisk hanya pantau.
        if ($role === 'manrisk') {
            abort(Response::HTTP_FORBIDDEN, 'Akses Ditolak! Divisi ManRisk hanya berwenang memantau, bukan mengubah progress penanganan.');
        }

        // Minimal bisa melihat laporan dulu.
        $this->ensureCanViewReport($report);
    }

    public function create($kategori)
    {
        // Validasi biar orang gak ngetik URL sembarangan
        if (!in_array($kategori, ['finansial', 'non-finansial'])) {
            abort(404, 'Kategori Risiko Tidak Ditemukan');
        }

        $userRole = $this->primaryRoleName();
        if (!$userRole) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak. Role user tidak ditemukan.');
        }

        // Tarik soal yang sesuai dengan JABATAN dan KATEGORI-nya
        $riskItems = RiskItem::with('causes.mitigations')
            ->where('role_target', $userRole)
            ->where('kategori', $kategori)
            ->get();

        return view('risk_reports.create', compact('riskItems', 'kategori'));
    }

    public function store(StoreRiskReportRequest $request)
    {
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
        $role = $user?->primaryRoleName();
        if (!$user || !$role) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
        }

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
                ->whereHas('item', function ($q) {
                    $q->where('role_target', 'kacab');
                })
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        return view('risk_reports.review', compact('reports', 'tindakLanjut'));
    }

    // PROSES APPROVAL
    public function updateStatus(UpdateRiskApprovalStatusRequest $request, $id)
    {
        $report = RiskReport::findOrFail($id);
        $this->ensureCanApproveReport($report);

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
        $role = $user?->primaryRoleName();
        if (!$user || !$role) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.');
        }

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
    public function updateResolution(UpdateRiskResolutionRequest $request, $id)
    {
        $report = RiskReport::findOrFail($id);
        $this->ensureCanUpdateProgress($report);

        $report->update(['resolution_status' => $request->resolution_status]);

        return redirect()->back()->with('success', 'Status tindak lanjut diperbarui!');
    }

    // FUNGSI TAMBAHAN: CATAT PROGRESS TINDAK LANJUT (NOTE + STATUS)
    public function addProgress(AddRiskProgressRequest $request, $id)
    {
        $user = Auth::user();
        $report = RiskReport::findOrFail($id);
        $this->ensureCanUpdateProgress($report);

        // 1. Cek jika user mencoba menutup laporan (Closed)
        // 1. Cek jika user mencoba menutup laporan (Closed)
        if ($request->new_status === 'closed') {
            
            // ATURAN A: PAKE == (BUKAN ===) BIAR NGGAK KETIPU TIPE DATA
            if ($user->hasRole('kacab') && $report->user_id == $user->id) {
                return back()->with('error', 'Akses Ditolak! Sebagai pelapor (Maker), Anda tidak boleh menutup kasus ini sendiri. Harap hubungi Korwil.');
            }

            // Aturan B: Selain Atasan (Kacab/Korwil/ManRisk) dilarang menutup
            if (!$user->hasAnyRole(['kacab', 'korwil', 'manrisk'])) {
                return back()->with('error', 'Hanya Atasan yang berwenang menutup laporan.');
            }
        }

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
        $this->ensureCanViewReport($report);

        return view('risk_reports.show', compact('report'));
    }
}
