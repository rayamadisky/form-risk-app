<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskItem;
use App\Models\RiskCause;
use App\Models\RiskMitigation; // Pastikan nama Model lu sesuai

class MasterRiskController extends Controller
{
    // 1. Nampilin Halaman Utama & Filter
    public function index(Request $request)
    {
        // Tarik data dengan metode Eager Loading biar query nggak bocor (N+1 Problem)
        $query = RiskItem::with('causes.mitigations');

        // Logic Filter Jabatan
        if ($request->filled('jabatan') && $request->jabatan !== 'semua') {
            $query->where('role_target', $request->jabatan);
        }

        $riskItems = $query->orderBy('kategori')->get();

        // Ambil daftar jabatan yang unik buat isi dropdown filter
        $roles = RiskItem::select('role_target')->distinct()->pluck('role_target');

        return view('master_risk.index', compact('riskItems', 'roles'));
    }

    // 2. Simpan Penyebab Baru
    public function storeCause(Request $request, $itemId)
    {
        $request->validate(['penyebab' => 'required|string|max:255']);
        RiskCause::create([
            'risk_item_id' => $itemId,
            'penyebab' => $request->penyebab
        ]);
        return back()->with('success', 'Penyebab baru berhasil ditambahkan!');
    }

    // 3. Simpan Mitigasi Baru
    public function storeMitigation(Request $request, $causeId)
    {
        $request->validate(['mitigasi' => 'required|string|max:255']);
        // Sesuaikan dengan nama model mitigasi lu
        \App\Models\RiskMitigation::create([
            'risk_cause_id' => $causeId,
            'mitigasi' => $request->mitigasi
        ]);
        return back()->with('success', 'Mitigasi berhasil ditambahkan!');
    }
}
