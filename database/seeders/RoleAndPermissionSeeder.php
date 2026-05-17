<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |----------------------------------------------------------------------
        | BUAT ROLE
        |----------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $spvKedisiplinan = Role::firstOrCreate(['name' => 'spv_kedisiplinan', 'guard_name' => 'web']);
        $pic = Role::firstOrCreate(['name' => 'pic', 'guard_name' => 'web']);
        $adminLab = Role::firstOrCreate(['name' => 'admin_lab', 'guard_name' => 'web']);
        $asistenLab = Role::firstOrCreate(['name' => 'asisten_lab', 'guard_name' => 'web']);

        /*
        |----------------------------------------------------------------------
        | ASSIGN PERMISSION KE ROLE
        |----------------------------------------------------------------------
        */

        // ── SUPER ADMIN & SPV KEDISIPLINAN: semua permission ──
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $superAdmin->givePermissionTo($allPermissions);
            $spvKedisiplinan->givePermissionTo($allPermissions);
        }

        // ── PIC ──
        $pic->givePermissionTo([
            // Laporan Keluhan (Menggunakan format :: sesuai Shield)
            'view_any_laporan::keluhan',
            'view_laporan::keluhan',

            // Perbaikan (Satu kata, tetap pakai underscore bawaan action)
            'view_any_perbaikan',
            'view_perbaikan',
            'update_perbaikan',

            // Riwayat Perbaikan (Menggunakan format :: sesuai Shield)
            'view_any_riwayat::perbaikan',
            'view_riwayat::perbaikan',

            // Halaman Kustom / Pages
            'page_KanbanBoard',
            'page_MonitoringDashboard',
            'page_CetakPdf',
        ]);

        // ── ADMIN LAB ──
        $adminLab->givePermissionTo([
            // Laporan (view only)
            'view_any_laporan::keluhan',
            'view_laporan::keluhan',

            // Perbaikan (full CRUD untuk update status)
            'view_any_perbaikan',
            'view_perbaikan',
            'update_perbaikan',

            // Riwayat (view)
            'view_any_riwayat::perbaikan',
            'view_riwayat::perbaikan',

            // Kanban
            'page_KanbanBoard',
        ]);

        // ── ASISTEN LAB ──
        $asistenLab->givePermissionTo([
            'view_any_laporan::keluhan',
            'view_laporan::keluhan',
            'view_any_perbaikan',
            'view_perbaikan',
            'view_any_riwayat::perbaikan',
            'view_riwayat::perbaikan',
        ]);

        /*
        |----------------------------------------------------------------------
        | ASSIGN ROLE KE USER
        |----------------------------------------------------------------------
        */

        $spvUser = User::where('email', 'spv.kedisiplinan@lab.id')->first();
        if ($spvUser) $spvUser->assignRole('spv_kedisiplinan');

        $spvJaringan = User::where('email', 'spv.jaringan@lab.id')->first();
        if ($spvJaringan) $spvJaringan->assignRole('pic'); 

        $adminLab1 = User::where('email', 'admin.lab1@lab.id')->first();
        if ($adminLab1) $adminLab1->assignRole('admin_lab');

        $adminLab2 = User::where('email', 'admin.lab2@lab.id')->first();
        if ($adminLab2) $adminLab2->assignRole('admin_lab');
    }
}