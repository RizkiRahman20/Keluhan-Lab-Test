<?php

namespace App\Policies;

use App\Models\Perbaikan;
use App\Models\User;

class PerbaikanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminLab() || $user->isSPV();
    }

    public function view(User $user, Perbaikan $perbaikan): bool
    {
        return $user->isAdminLab() || $user->isSPV();
    }

    public function create(User $user): bool
    {
        return false; // Dibuat otomatis saat laporan disetujui
    }

    public function update(User $user, Perbaikan $perbaikan): bool
    {
        return $user->isAdminLab();
    }

    public function delete(User $user, Perbaikan $perbaikan): bool
    {
        return $user->isSPVKedisiplinan();
    }
}