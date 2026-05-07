<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasName;

class User extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nm_user',
        'email',
        'password',
        'role_user',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Akses ke panel Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status_aktif === 'aktif';
    }

    // Cek apakah user adalah SPV
    public function isSPV(): bool
    {
        return str_starts_with($this->role_user, 'spv_');
    }

    // Cek apakah user adalah SPV Kedisiplinan
    public function isSPVKedisiplinan(): bool
    {
        return $this->role_user === 'spv_kedisiplinan';
    }

    // Cek apakah user adalah Admin Lab
    public function isAdminLab(): bool
    {
        return $this->role_user === 'admin_lab';
    }

    public function penugasanUserLabs(): HasMany
    {
        return $this->hasMany(PenugasanUserLab::class, 'id_user', 'id_user' );
    }

    public function laporanKeluhans(): HasMany
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_user', 'id_user');
    }

    // Lab yang ditugaskan ke user ini (melalui penugasan)
    public function labs()
    {
        return $this->belongsToMany(Lab::class, 'penugasan_user_labs', 'id_user', 'id_lab', 'id_user', 'id_lab')
            ->withPivot(['id_penugasan', 'status_aktif', 'semester', 'tahun_ajaran'])
            ->wherePivot('status_aktif', 'aktif');
    }

    public function getFilamentName(): string
    {
        return $this->nm_user;
    }
}