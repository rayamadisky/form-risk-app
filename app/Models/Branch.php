<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    use HasFactory;

    protected $fillable = [
        'nama_cabang', // (ini nama kolom lama lu, biarin aja)
        'is_active',   // <--- TAMBAHIN INI
        'korwil_id',   // <--- TAMBAHIN INI
    ];
    // Cabang ini di bawah pengawasan siapa?
    public function korwil()
    {
        return $this->belongsTo(User::class, 'korwil_id');
    }

    // Scope buat Maker: Hanya nampilin cabang yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
