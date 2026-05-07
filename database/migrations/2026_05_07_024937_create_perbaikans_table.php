<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbaikans', function (Blueprint $table) {
            $table->id('id_perbaikan');
            $table->enum('status_perbaikan', [
                'antrean',
                'dikerjakan',
                'menunggu_sparepart',
                'selesai'
            ])->default('antrean');
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('ft_perbaikan')->nullable(); // foto bukti perbaikan
            $table->text('catatan_pbk')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->enum('app_validasi', ['menunggu', 'divalidasi', 'dikembalikan'])->default('menunggu');
            $table->string('id_laporan');
            $table->foreign('id_laporan')->references('no_laporan')->on('laporan_keluhans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};