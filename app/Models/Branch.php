<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    // Cabang ini di bawah pengawasan siapa?
    public function korwil()
    {
        return $this->belongsTo(User::class, 'korwil_id');
    }
}
