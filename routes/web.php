<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Admin\RiskMasterController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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
});

require __DIR__ . '/auth.php';