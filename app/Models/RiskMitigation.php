<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMitigation extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi balik: Mitigasi ini milik 1 Penyebab
    public function cause()
    {
        return $this->belongsTo(RiskCause::class);
    }
}