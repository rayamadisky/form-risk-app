<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Admin\RiskMasterController;
use App\Models\RiskReport;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $userBranchId = $user->branch_id;
    $role = $user?->primaryRoleName();

    // Tentukan branch IDs yang bisa dilihat user
    if ($role === 'korwil') {
        $branchIds = \App\Models\Branch::where('korwil_id', $user->id)
            ->where('is_active', true)
            ->pluck('id');
    } elseif ($role === 'kacab') {
        $branchIds = collect([$userBranchId]);
    } elseif ($role === 'manrisk') {
        $branchIds = \App\Models\Branch::where('is_active', true)->pluck('id');
    } else {
        // Maker (teller/ca/csr/security) — lihat laporan sendiri
        $branchIds = collect();
    }

    // Laporan terbaru (untuk tabel)
    if (in_array($role, ['korwil', 'kacab', 'manrisk'])) {
        $recentReports = RiskReport::with(['user', 'branch', 'item'])
            ->whereIn('branch_id', $branchIds)
            ->latest()
            ->take(10)
            ->get();
    } else {
        // Teller/CA/CSR/Security — lihat laporan sendiri
        $recentReports = RiskReport::with(['user', 'branch', 'item'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
    }

    // Stat cards — difilter sesuai role
    $reportQuery = RiskReport::query();
    if ($role === 'korwil') {
        $reportQuery->whereIn('branch_id', $branchIds);
    } elseif ($role === 'kacab') {
        $reportQuery->where('branch_id', $userBranchId);
    } elseif (in_array($role, ['teller', 'ca', 'csr', 'security'])) {
        $reportQuery->where('user_id', $user->id);
    }
    // ManRisk ga perlu filter

    $totalLaporanBulanIni = (clone $reportQuery)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $totalPending = (clone $reportQuery)
        ->where(function ($q) {
            $q->where('approval_status', 'pending')
              ->orWhere('approval_status', 'pending_kacab')
              ->orWhere('approval_status', 'pending_korwil');
        })
        ->count();

    $totalApproved = (clone $reportQuery)
        ->where('approval_status', 'approved')
        ->count();

    $totalLossApproved = (clone $reportQuery)
        ->where('approval_status', 'approved')
        ->where('kategori', 'finansial')
        ->sum('dampak_finansial');

    // Hitung badge pending untuk checker
    $pendingCount = 0;
    if ($role === 'kacab') {
        $pendingCount = RiskReport::where('branch_id', $userBranchId)
            ->where('approval_status', 'pending_kacab')
            ->count();
    } elseif ($role === 'korwil') {
        $pendingCount = RiskReport::whereIn('branch_id', $branchIds)
            ->where('approval_status', 'pending_korwil')
            ->count();
    }

    return view('dashboard', compact(
        'recentReports',
        'totalLaporanBulanIni',
        'totalPending',
        'totalApproved',
        'totalLossApproved',
        'pendingCount',
        'role'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// AREA PENGGUNA LOGIN (MAKER & CHECKER)
// Semua rute di sini udah dibungkus 1 gembok auth biar nggak numpuk
// =========================================================================
Route::middleware('auth')->group(function () {

    // --- Profile Bawaan Laravel Breeze ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MENU 1: INPUT LAPORAN (MAKER) ---
    Route::get('/form-risiko/{kategori}', [RiskReportController::class, 'create'])->name('form.risiko');
    Route::post('/form-risiko', [RiskReportController::class, 'store'])->name('form.risiko.store');

    // --- MENU 2: REVIEW & TINDAK LANJUT (CHECKER: KACAB & KORWIL) ---
    Route::get('/review-laporan', [RiskReportController::class, 'review'])->name('review.laporan');

    // Ini perbaikan rute persetujuan (Approve/Reject)
    Route::post('/risk-reports/{id}/status', [RiskReportController::class, 'updateStatus'])->name('risk_reports.update_status');

    // Ini rute baru buat Tindak Lanjut (Monitoring/Closed)
    Route::post('/risk-reports/{id}/resolution', [RiskReportController::class, 'updateResolution'])->name('risk_reports.update_resolution');

    // --- MENU 3: RIWAYAT KESELURUHAN ---
    Route::get('/riwayat-risiko', [RiskReportController::class, 'index'])->name('risk.history');

    // Rute Detail & Progress Laporan
    Route::get('/risk-report/{id}', [RiskReportController::class, 'show'])->name('risk_reports.show');

    // Rute untuk nambahin Progress Catatan (Action POST dari halaman show)
    Route::post('/risk-report/{id}/progress', [RiskReportController::class, 'addProgress'])->name('risk_reports.add_progress');
});


// =========================================================================
// AREA KHUSUS DEWA APLIKASI (MANAJEMEN RISIKO)
// =========================================================================
Route::middleware(['auth', 'role:manrisk'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle');

    // CRUD Bank Soal
    Route::get('/admin/risk-master', [RiskMasterController::class, 'index'])->name('admin.risk_master.index');
    Route::post('/admin/risk-master/item', [RiskMasterController::class, 'storeItem'])->name('admin.risk_master.store_item');
    Route::post('/admin/risk-master/item/{id}/cause', [RiskMasterController::class, 'storeCause'])->name('admin.risk_master.store_cause');
    Route::delete('/admin/risk-master/item/{id}', [RiskMasterController::class, 'destroyItem'])->name('admin.risk_master.destroy_item');

    // ... rute manrisk yang lain ...

    // CRUD User Management
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');

    // Rute buat update penyebab & mitigasi
    Route::patch('/admin/risk-master/cause/{id}', [\App\Http\Controllers\Admin\RiskMasterController::class, 'updateCause'])->name('admin.risk_master.update_cause');

    // Rute tambah mitigasi ke cause yang sudah ada (terpisah dari storeCause)
    Route::post('/admin/risk-master/cause/{causeId}/mitigation', [\App\Http\Controllers\Admin\RiskMasterController::class, 'storeMitigation'])->name('admin.risk_master.store_mitigation');

    // Rute Manajemen Master Data Cabang (Khusus ManRisk)
    Route::get('/branches-management', [App\Http\Controllers\BranchManagementController::class, 'index'])->name('branches.index');
    Route::put('/branches-management/{id}', [App\Http\Controllers\BranchManagementController::class, 'update'])->name('branches.update');
    Route::post('/branches-management', [App\Http\Controllers\BranchManagementController::class, 'store'])->name('branches.store');

});

require __DIR__ . '/auth.php';
