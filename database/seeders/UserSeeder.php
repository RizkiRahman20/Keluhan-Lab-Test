<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SPV Kedisiplinan (superadmin sistem)
        User::create([
            'nm_user' => 'SPV Kedisiplinan',
            'email' => 'spv.kedisiplinan@lab.id',
            'password' => Hash::make('password'),
            'role_user' => 'spv_kedisiplinan',
            'status_aktif' => 'aktif',
        ]);

        // SPV Jaringan
        User::create([
            'nm_user' => 'SPV Jaringan',
            'email' => 'spv.jaringan@lab.id',
            'password' => Hash::make('password'),
            'role_user' => 'spv_jaringan',
            'status_aktif' => 'aktif',
        ]);

        // Admin Lab 1
        User::create([
            'nm_user' => 'Admin Lab 1',
            'email' => 'admin.lab1@lab.id',
            'password' => Hash::make('password'),
            'role_user' => 'admin_lab',
            'status_aktif' => 'aktif',
        ]);

        // Admin Lab 2
        User::create([
            'nm_user' => 'Admin Lab 2',
            'email' => 'admin.lab2@lab.id',
            'password' => Hash::make('password'),
            'role_user' => 'admin_lab',
            'status_aktif' => 'aktif',
        ]);
    }
}