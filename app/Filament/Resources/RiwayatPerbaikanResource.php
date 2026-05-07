<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPerbaikanResource\Pages;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RiwayatPerbaikanResource extends Resource
{
    protected static ?string $model = RiwayatPerbaikan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $label = 'Riwayat Perbaikan';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPV() || Auth::user()?->isAdminLab();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('perbaikan.id_laporan')->label('No. Laporan'),
                Tables\Columns\TextColumn::make('perbaikan.laporan.penugasan.lab.nm_lab')->label('Lab'),
                Tables\Columns\TextColumn::make('tgl_ubah')->label('Tanggal Ubah')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('catatan_rw')->label('Catatan')->limit(50),
                Tables\Columns\TextColumn::make('perbaikan.status_perbaikan')->label('Status Akhir'),
            ])
            ->filters([
                Tables\Filters\Filter::make('tgl_ubah')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q) => $q->whereDate('tgl_ubah', '>=', $data['dari']))
                            ->when($data['sampai'], fn ($q) => $q->whereDate('tgl_ubah', '<=', $data['sampai']));
                    }),
            ])
            ->defaultSort('tgl_ubah', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatPerbaikans::route('/'),
        ];
    }
}