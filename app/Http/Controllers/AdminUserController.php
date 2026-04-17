<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        // Narik semua data User. 
        // Gua pake 'with' (Eager Loading) biar database lu nggak ngos-ngosan (N+1 Problem) pas narik relasi cabang & jabatan.
        $users = User::with(['roles', 'branch'])->orderBy('name', 'asc')->get();
        
        return view('admin.users.index', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        // Proteksi: Admin nggak boleh non-aktifin dirinya sendiri
        if (auth()->id === $user->id) {
            return back()->with('error', 'Tindakan ditolak! Anda tidak bisa menonaktifkan akun sendiri.');
        }

        // Saklar On/Off: Kebalikan dari status yang sekarang
        $user->update(['is_active' => !$user->is_active]);

        $statusMessage = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$statusMessage}.");
    }
}