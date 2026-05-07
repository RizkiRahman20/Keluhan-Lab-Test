<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeluhanResource\Pages;
use App\Models\LaporanKeluhan;
use App\Models\Perbaikan;
use App\Models\PenugasanUserLab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanKeluhanResource extends Resource
{
    protected static ?string $model = LaporanKeluhan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $label = 'Laporan Keluhan';
    protected static ?string $pluralLabel = 'Daftar Laporan Masuk';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user?->isSPV() || $user?->isAdminLab();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('no_laporan')->label('No. Laporan')->disabled(),
            Forms\Components\TextInput::make('nim_pelapor')->label('NIM Pelapor')->disabled(),
            Forms\Components\TextInput::make('nm_pelapor')->label('Nama Pelapor')->disabled(),
            Forms\Components\TextInput::make('fakultas_pelapor')->label('Fakultas')->disabled(),
            Forms\Components\Select::make('kategori')
                ->label('Kategori')
                ->options(['PC' => 'PC', 'non_PC' => 'Non PC'])
                ->disabled(),
            Forms\Components\Textarea::make('catatan_lpr')->label('Catatan Keluhan')->disabled(),
            Forms\Components\Select::make('approval')
                ->label('Status Approval')
                ->options([
                    'menunggu' => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                ])
                // Gunakan de-select/disabled jika hanya ingin melihat
                ->visible(fn () => Auth::user()?->isSPV()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_laporan')->label('No. Laporan')->searchable(),
                Tables\Columns\TextColumn::make('tgl_lapor')->label('Tgl Lapor')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('nm_pelapor')->label('Pelapor')->searchable(),
                Tables\Columns\TextColumn::make('penugasan.lab.nm_lab')->label('Lab'),
                
                // Perbaikan: Filament v3 menggunakan TextColumn dengan badge()
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PC' => 'warning',
                        'non_PC' => 'info',
                    }),
                
                Tables\Columns\TextColumn::make('approval')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(['PC' => 'PC', 'non_PC' => 'Non PC']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LaporanKeluhan $record) =>
                        Auth::user()?->isSPV() && $record->approval === 'menunggu'
                    )
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('id_penugasan')
                            ->label('Delegasikan ke Admin Lab')
                            ->options(function () {
                                return PenugasanUserLab::with(['user', 'lab'])
                                    ->where('status_aktif', 'aktif')
                                    ->whereHas('user', fn ($q) => $q->where('role_user', 'admin_lab'))
                                    ->get()
                                    ->mapWithKeys(fn ($p) => [
                                        $p->id_penugasan => ($p->user->nm_user ?? 'N/A') . ' - ' . ($p->lab->nm_lab ?? 'N/A')
                                    ]);
                            })
                            ->required(),
                    ])
                    ->action(function (LaporanKeluhan $record, array $data) {
                        $record->update([
                            'approval' => 'disetujui',
                            'id_user' => Auth::id(),
                            'id_penugasan' => $data['id_penugasan'],
                        ]);
                        Perbaikan::create([
                            'status_perbaikan' => 'antrean',
                            'id_laporan' => $record->no_laporan,
                        ]);
                        Notification::make()->title('Laporan disetujui!')->success()->send();
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LaporanKeluhan $record) =>
                        Auth::user()?->isSPV() && $record->approval === 'menunggu'
                    )
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (LaporanKeluhan $record, array $data) {
                        $record->update([
                            'approval' => 'ditolak',
                            'id_user' => Auth::id(),
                        ]);
                        Perbaikan::create([
                            'status_perbaikan' => 'selesai',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                            'id_laporan' => $record->no_laporan,
                        ]);
                        Notification::make()->title('Laporan ditolak.')->warning()->send();
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) return $query;

        // Perbaikan Utama: Tambahkan nama tabel pada pluck agar tidak ambiguous
        if ($user->isAdminLab() || ($user->isSPV() && !$user->isSPVKedisiplinan())) {
            // Kita ambil id_lab secara spesifik dari tabel penugasan_user_labs
            $labIds = $user->labs()->pluck('penugasan_user_labs.id_lab')->toArray();
            
            $query->whereHas('penugasan', function ($q) use ($labIds) {
                $q->whereIn('penugasan_user_labs.id_lab', $labIds);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeluhans::route('/'),
            'view' => Pages\ViewLaporanKeluhan::route('/{record}'),
        ];
    }
}