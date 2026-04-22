<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiskItem;
use App\Models\RiskCause;
use App\Models\RiskMitigation;
use Illuminate\Http\Request;

class RiskMasterController extends Controller
{
    // 1. LIHAT SEMUA DAFTAR PERTANYAAN
    public function index()
    {
        $riskItems = RiskItem::with('causes.mitigations')->orderBy('role_target')->get();
        return view('admin.risk_master.index', compact('riskItems'));
    }

    // 2. SIMPAN PERTANYAAN BARU
    public function storeItem(Request $request)
    {
        $request->validate([
            'nama_risiko' => 'required|string|max:255',
            'kategori' => 'required|in:finansial,non-finansial', // TAMBAHAN INI
            'role_target' => 'required|in:teller,ca,csr,security,kacab,korwil',
        ]);

        RiskItem::create($request->all());
        return back()->with('success', 'Pertanyaan risiko baru berhasil ditambahkan!');
    }

    // 3. SIMPAN PENYEBAB & MITIGASI (Bunderling)
    public function storeCause(Request $request, $itemId)
    {
        $request->validate([
            'penyebab' => 'required|string',
            'mitigasi' => 'nullable|string',
        ]);

        $cause = RiskCause::create([
            'risk_item_id' => $itemId,
            'penyebab' => $request->penyebab
        ]);

        if ($request->mitigasi) {
            RiskMitigation::create([
                'risk_cause_id' => $cause->id,
                'mitigasi' => $request->mitigasi
            ]);
        }

        return back()->with('success', 'Penyebab dan Mitigasi berhasil ditambahkan!');
    }

    // 4. HAPUS PERTANYAAN (Gua kasih fitur hapus karena ini Master Data, bukan data transaksi)
    public function destroyItem($id)
    {
        RiskItem::findOrFail($id)->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus dari sistem.');
    }

    // FUNGSI UPDATE PENYEBAB & MITIGASI (INLINE)
    public function updateCause(Request $request, $id)
    {
        $request->validate([
            'penyebab' => 'required|string|max:255',
            'mitigasi' => 'nullable|string|max:255'
        ]);

        // 1. Cari dan Update Penyebab
        $cause = \App\Models\RiskCause::findOrFail($id);
        $cause->update(['penyebab' => $request->penyebab]);

        // 2. Eksekusi Mitigasi (Update, Create, atau Delete)
        if ($request->filled('mitigasi')) {
            $mitigation = $cause->mitigations()->first();
            if ($mitigation) {
                $mitigation->update(['mitigasi' => $request->mitigasi]);
            } else {
                $cause->mitigations()->create(['mitigasi' => $request->mitigasi]);
            }
        } else {
            // Kalau form mitigasi dikosongin, hapus data mitigasi yang nempel
            $cause->mitigations()->delete();
        }

        return back()->with('success', 'Data Penyebab & Mitigasi berhasil diperbarui!');
    }
}
