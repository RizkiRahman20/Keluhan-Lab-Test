<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_keluhans', function (Blueprint $table) {
            $table->string('no_laporan', 20)->primary();
            $table->date('tgl_lapor');
            $table->enum('approval', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->string('nim_pelapor', 20);
            $table->string('nm_pelapor', 100);
            $table->string('fakultas_pelapor', 100);
            $table->enum('kategori', ['PC', 'non_PC']); // kategori kerusakan
            $table->text('catatan_lpr');
            $table->string('file_foto')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete(); // PIC yang memvalidasi
            $table->foreignId('id_penugasan')->nullable()->constrained('penugasan_user_labs', 'id_penugasan')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_keluahns');
    }
};