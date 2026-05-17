<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerbaikanResource\Pages;
use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PerbaikanResource extends Resource
{
    protected static ?string $model = Perbaikan::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Perbaikan';
    protected static ?string $label = 'Perbaikan';
    protected static ?string $pluralLabel = 'Kelola Perbaikan';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('view_any_perbaikan');
    }
    
    public static function canUpdate(): bool
    {
        return Auth::user()?->can('update_perbaikan');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status_perbaikan')
                ->label('Status Perbaikan')
                ->options([
                    'antrean' => 'Antrean',
                    'dikerjakan' => 'Dikerjakan',
                    'menunggu_sparepart' => 'Menunggu Sparepart',
                    'selesai' => 'Selesai',
                ])
                ->required(),
            Forms\Components\DatePicker::make('tgl_mulai')->label('Tanggal Mulai'),
            Forms\Components\DatePicker::make('tgl_selesai')->label('Tanggal Selesai'),
            Forms\Components\FileUpload::make('ft_perbaikan')
                ->label('Foto Bukti Perbaikan')
                ->image()
                ->directory('perbaikan'),
            Forms\Components\Textarea::make('catatan_pbk')->label('Catatan Perbaikan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_laporan')->label('No. Laporan'),
                Tables\Columns\TextColumn::make('laporan.penugasan.lab.nm_lab')->label('Lab'),
                Tables\Columns\TextColumn::make('laporan.nm_pelapor')->label('Pelapor'),
                Tables\Columns\BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->colors([
                        'gray' => 'antrean',
                        'warning' => 'dikerjakan',
                        'info' => 'menunggu_sparepart',
                        'success' => 'selesai',
                    ]),
                Tables\Columns\BadgeColumn::make('app_validasi')
                    ->label('Validasi SPV')
                    ->colors([
                        'gray' => 'menunggu',
                        'success' => 'divalidasi',
                        'danger' => 'dikembalikan',
                    ]),
                Tables\Columns\TextColumn::make('tgl_mulai')->label('Mulai')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tgl_selesai')->label('Selesai')->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_perbaikan')
                    ->label('Status')
                    ->options([
                        'antrean' => 'Antrean',
                        'dikerjakan' => 'Dikerjakan',
                        'menunggu_sparepart' => 'Menunggu Sparepart',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()?->isAdminLab()),
                // Update Status
                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Perbaikan $record) =>
                        Auth::user()?->hasRole('admin_lab') && $record->status_perbaikan !== 'selesai'
                    )
                    ->form([
                        Forms\Components\Select::make('status_perbaikan')
                            ->label('Status Baru')
                            ->options([
                                'dikerjakan' => 'Dikerjakan',
                                'menunggu_sparepart' => 'Menunggu Sparepart',
                                'selesai' => 'Selesai',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_rw')
                            ->label('Catatan Perubahan')
                            ->required(),
                        Forms\Components\FileUpload::make('ft_perbaikan')
                            ->label('Upload Bukti (jika selesai)')
                            ->image()
                            ->directory('perbaikan'),
                    ])
                    ->action(function (Perbaikan $record, array $data) {
                        $updateData = [
                            'status_perbaikan' => $data['status_perbaikan'],
                            'catatan_pbk' => $data['catatan_rw'],
                        ];
                        if ($data['status_perbaikan'] === 'selesai') {
                            $updateData['tgl_selesai'] = now()->toDateString();
                            if (!empty($data['ft_perbaikan'])) {
                                $updateData['ft_perbaikan'] = $data['ft_perbaikan'];
                            }
                        }
                        if ($data['status_perbaikan'] === 'dikerjakan' && !$record->tgl_mulai) {
                            $updateData['tgl_mulai'] = now()->toDateString();
                        }
                        $record->update($updateData);
                        // Simpan ke riwayat
                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => $data['catatan_rw'],
                            'id_perbaikan' => $record->id_perbaikan,
                        ]);
                        Notification::make()->title('Status berhasil diperbarui!')->success()->send();
                    }),
                // Validasi oleh SPV
                Action::make('validasi')
                    ->label('Validasi Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Perbaikan $record) =>
                        Auth::user()?->hasRole('pic') &&
                        $record->status_perbaikan === 'selesai' &&
                        $record->app_validasi === 'menunggu'
                    )
                    ->requiresConfirmation()
                    ->action(function (Perbaikan $record) {
                        $record->update(['app_validasi' => 'divalidasi']);
                        Notification::make()->title('Perbaikan divalidasi!')->success()->send();
                    }),
                // Kembalikan oleh SPV
                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (Perbaikan $record) =>
                        Auth::user()?->isSPV() &&
                        $record->status_perbaikan === 'selesai' &&
                        $record->app_validasi === 'menunggu'
                    )
                    ->form([
                        Forms\Components\Textarea::make('alasan')->label('Alasan Dikembalikan')->required(),
                    ])
                    ->action(function (Perbaikan $record, array $data) {
                        $record->update([
                            'app_validasi' => 'dikembalikan',
                            'status_perbaikan' => 'dikerjakan',
                            'alasan_penolakan' => $data['alasan'],
                        ]);
                        Notification::make()->title('Perbaikan dikembalikan ke Admin.')->warning()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerbaikans::route('/'),
            'edit' => Pages\EditPerbaikan::route('/{record}/edit'),
        ];
    }
}