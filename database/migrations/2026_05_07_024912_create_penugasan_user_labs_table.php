<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_user_labs', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->enum('status_aktif', ['aktif', 'nonaktif'])->default('aktif');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('tahun_ajaran', 10); // contoh: 2024/2025
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_lab')->constrained('labs', 'id_lab')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_user_labs');
    }
};