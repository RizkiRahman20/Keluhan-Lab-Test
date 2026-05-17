<?php

namespace App\Filament\Pages;

use App\Models\LaporanKeluhan;
use App\Models\Perbaikan;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;

class MonitoringDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = 'Dashboard Monitoring';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.monitoring-dashboard';

    public function getViewData(): array
    {
        return [
            'totalLaporan' => LaporanKeluhan::count(),
            'laporanMenunggu' => LaporanKeluhan::where('approval', 'menunggu')->count(),
            'laporanDisetujui' => LaporanKeluhan::where('approval', 'disetujui')->count(),
            'perbaikanBerjalan' => Perbaikan::whereIn('status_perbaikan', ['dikerjakan', 'antrean'])->count(),
            'perbaikanSelesai' => Perbaikan::where('status_perbaikan', 'selesai')->count(),
            'menungguValidasi' => Perbaikan::where('app_validasi', 'menunggu')
                ->where('status_perbaikan', 'selesai')->count(),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->can('page_MonitoringDashboard');
    }
}