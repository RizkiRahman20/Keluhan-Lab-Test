<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_keluhans', function (Blueprint $table) {
            $table->foreignId('id_lab')
                  ->nullable()
                  ->after('id_penugasan')
                  ->constrained('labs', 'id_lab')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_keluhans', function (Blueprint $table) {
            $table->dropForeign(['id_lab']);
            $table->dropColumn('id_lab');
        });
    }
};