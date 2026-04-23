<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class BranchManagementController extends Controller
{
    // Update struktur cabang: Korwil dan Status Aktif
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'korwil_id' => 'nullable|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        $branch->update([
            'korwil_id' => $request->korwil_id,
            'is_active' => $request->is_active
        ]);

        return back()->with('success', 'Struktur Cabang ' . $branch->nama_cabang . ' berhasil diperbarui!');
    }
    public function index()
    {
        // 1. Tarik semua cabang
        $branches = \App\Models\Branch::orderBy('nama_cabang', 'asc')->get();

        // 2. Tarik semua user yang jabatannya (role) Korwil
        // Asumsi lu pake Spatie Permission, pakai role 'korwil'
        $listKorwil = \App\Models\User::role('korwil')->orderBy('name', 'asc')->get();

        return view('branches.index', compact('branches', 'listKorwil'));
    }
}
