<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName; // 1. Ditambahkan untuk kustomisasi nama profil
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName // 2. Implementasikan HasName di sini
{
    use Notifiable;
    use HasRoles;
    // Hapus HasPanelShield — kita override sendiri canAccessPanel()
    // use HasPanelShield;

    protected $table      = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nm_user',
        'email',
        'password',
        'role_user',
        'status_aktif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    /**
     * 3. Solusi Error: Beritahu Filament untuk membaca 'nm_user' sebagai nama user
     */
    public function getFilamentName(): string
    {
        return $this->nm_user ?? 'User';
    }

    /**
     * Override canAccessPanel — gunakan kolom status_aktif milik kita sendiri,
     * tidak bergantung pada role Spatie agar login tidak terkunci.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status_aktif === 'aktif';
    }

    // ── Helper methods ──

    public function isSPVKedisiplinan(): bool
    {
        // Cek dari kolom role_user ATAU dari Spatie role
        return $this->role_user === 'spv_kedisiplinan'
            || $this->hasRole('spv_kedisiplinan')
            || $this->hasRole('super_admin');
    }

    public function isSPV(): bool
    {
        return str_starts_with($this->role_user ?? '', 'spv_')
            || $this->hasAnyRole(['spv_kedisiplinan', 'pic', 'super_admin']);
    }

    public function isAdminLab(): bool
    {
        return $this->role_user === 'admin_lab'
            || $this->hasRole('admin_lab');
    }

    public function isPIC(): bool
    {
        return $this->hasRole('pic')
            || ($this->isSPV() && $this->penugasanUserLabs()
                ->where('status_aktif', 'aktif')->exists());
    }

    public function penugasanUserLabs(): HasMany
    {
        return $this->hasMany(PenugasanUserLab::class, 'id_user', 'id_user');
    }

    /**
     * Diperbaiki dari typo: laporanKeluahns -> laporanKeluhans
     */
    public function laporanKeluhans(): HasMany
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_user', 'id_user');
    }

    public function labs()
    {
        return $this->belongsToMany(
            Lab::class,
            'penugasan_user_labs',
            'id_user', 'id_lab',
            'id_user', 'id_lab'
        )
        ->withPivot(['id_penugasan', 'status_aktif', 'semester', 'tahun_ajaran'])
        ->wherePivot('status_aktif', 'aktif');
    }
}