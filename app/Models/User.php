<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // <-- 1. TAMBAHIN INI DI DERETAN ATAS

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'branch_id', // Pastikan branch_id juga udah lu tambahin di $fillable ya biar seeder bisa masukin data
        'is_active', // <-- TAMBAHIN INI
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Kasih tau Laravel kalau User ini kerja di sebuah Cabang
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Kalau User ini adalah Korwil, dia megang cabang mana aja?
    public function supervisedBranches()
    {
        return $this->hasMany(Branch::class, 'korwil_id');
    }

    public function primaryRoleName(): ?string
    {
        $role = $this->getRoleNames()->first();
        return $role ? (string) $role : null;
    }
    
}
