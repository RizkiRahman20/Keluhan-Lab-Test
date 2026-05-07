<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\PenugasanUserLab;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $spvJaringan = User::where('email', 'spv.jaringan@lab.id')->first();
        $adminLab1 = User::where('email', 'admin.lab1@lab.id')->first();
        $adminLab2 = User::where('email', 'admin.lab2@lab.id')->first();
        $lab1 = Lab::where('kd_lab', 'LAB01')->first();
        $lab2 = Lab::where('kd_lab', 'LAB02')->first();

        // SPV Jaringan jadi PIC Lab 1 dan Lab 2
        PenugasanUserLab::create([
            'status_aktif' => 'aktif',
            'semester' => 'ganjil',
            'tahun_ajaran' => '2024/2025',
            'id_user' => $spvJaringan->id_user,
            'id_lab' => $lab1->id_lab,
        ]);

        PenugasanUserLab::create([
            'status_aktif' => 'aktif',
            'semester' => 'ganjil',
            'tahun_ajaran' => '2024/2025',
            'id_user' => $spvJaringan->id_user,
            'id_lab' => $lab2->id_lab,
        ]);

        // Admin Lab 1 ditugaskan ke Lab 1
        PenugasanUserLab::create([
            'status_aktif' => 'aktif',
            'semester' => 'ganjil',
            'tahun_ajaran' => '2024/2025',
            'id_user' => $adminLab1->id_user,
            'id_lab' => $lab1->id_lab,
        ]);

        // Admin Lab 2 ditugaskan ke Lab 2
        PenugasanUserLab::create([
            'status_aktif' => 'aktif',
            'semester' => 'ganjil',
            'tahun_ajaran' => '2024/2025',
            'id_user' => $adminLab2->id_user,
            'id_lab' => $lab2->id_lab,
        ]);
    }
}