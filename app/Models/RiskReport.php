<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'branch_id', 'tanggal_kejadian', 'tanggal_diketahui',
        'risk_item_id', 'other_item_description', 'risk_cause_id',
        'other_cause_description', 'mitigasi_tambahan', 
        'dampak_finansial', 
        'dampak_non_finansial', // TAMBAHIN INI
        'skala_dampak', // <-- TAMBAHIN INI
        'kategori',             // TAMBAHIN INI JUGA
        'approval_status', 'resolution_status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function item() {
        return $this->belongsTo(RiskItem::class, 'risk_item_id');
    }

    public function cause() {
        return $this->belongsTo(RiskCause::class, 'risk_cause_id');
    }
    public function logs() {
    return $this->hasMany(RiskReportLog::class)->latest();
}
}