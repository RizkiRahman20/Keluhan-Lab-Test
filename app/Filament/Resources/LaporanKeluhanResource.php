<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeluhanResource\Pages;
use App\Models\LaporanKeluhan;
use App\Models\Perbaikan;
use App\Models\PenugasanUserLab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanKeluhanResource extends Resource
{
    protected static ?string $model        = LaporanKeluhan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $label          = 'Laporan Keluhan';
    protected static ?string $pluralLabel    = 'Daftar Laporan Masuk';
    protected static ?int    $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user?->isSPV() || $user?->isAdminLab();
    }

    /*
    |--------------------------------------------------------------------------
    | INFOLIST — Halaman View (bukan Edit), menampilkan detail + foto
    |--------------------------------------------------------------------------
    */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Informasi Pelapor')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    TextEntry::make('no_laporan')
                        ->label('No. Laporan')
                        ->weight('bold')
                        ->color('primary'),

                    TextEntry::make('tgl_lapor')
                        ->label('Tanggal Lapor')
                        ->date('d F Y'),

                    TextEntry::make('nim_pelapor')
                        ->label('NIM'),

                    TextEntry::make('nm_pelapor')
                        ->label('Nama Pelapor')
                        ->weight('bold'),

                    TextEntry::make('fakultas_pelapor')
                        ->label('Fakultas / Prodi'),

                    TextEntry::make('penugasan.lab.nm_lab')
                        ->label('Laboratorium')
                        ->weight('bold')
                        ->default('—'),
                ]),

            Section::make('Detail Keluhan')
                ->icon('heroicon-o-exclamation-triangle')
                ->columns(2)
                ->schema([
                    TextEntry::make('kategori')
                        ->label('Kategori Kerusakan')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'PC'     => 'warning',
                            'non_PC' => 'info',
                            default  => 'gray',
                        }),

                    TextEntry::make('approval')
                        ->label('Status Approval')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'menunggu'  => 'warning',
                            'disetujui' => 'success',
                            'ditolak'   => 'danger',
                            default     => 'gray',
                        }),

                    TextEntry::make('catatan_lpr')
                        ->label('Deskripsi Keluhan')
                        ->columnSpanFull()
                        ->prose(),
                ]),

            // ── INI BAGIAN FOTO ──
            Section::make('Foto Kerusakan')
                ->icon('heroicon-o-photo')
                ->schema([
                    ImageEntry::make('file_foto')
                        ->label('')          // kosongkan label agar foto full
                        ->disk('public')     // wajib: disk public
                        ->height(400)        // tinggi preview
                        ->extraImgAttributes(['style' => 'border-radius:12px;object-fit:cover;width:100%;'])
                        ->visible(fn ($record) => filled($record->file_foto)),

                    TextEntry::make('no_foto_placeholder')
                        ->label('')
                        ->default('Mahasiswa tidak melampirkan foto kerusakan.')
                        ->color('gray')
                        ->visible(fn ($record) => !filled($record->file_foto)),
                ]),

            Section::make('Informasi Perbaikan')
                ->icon('heroicon-o-wrench-screwdriver')
                ->columns(2)
                ->visible(fn ($record) => $record->perbaikan !== null)
                ->schema([
                    TextEntry::make('perbaikan.status_perbaikan')
                        ->label('Status Perbaikan')
                        ->badge()
                        ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucfirst($state)))
                        ->color(fn ($state) => match ($state) {
                            'antrean'            => 'gray',
                            'dikerjakan'         => 'warning',
                            'menunggu_sparepart' => 'info',
                            'selesai'            => 'success',
                            default              => 'gray',
                        }),

                    TextEntry::make('perbaikan.app_validasi')
                        ->label('Validasi SPV')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'menunggu'    => 'warning',
                            'divalidasi'  => 'success',
                            'dikembalikan'=> 'danger',
                            default       => 'gray',
                        }),

                    TextEntry::make('perbaikan.tgl_mulai')
                        ->label('Tanggal Mulai')
                        ->date('d F Y')
                        ->placeholder('—'),

                    TextEntry::make('perbaikan.tgl_selesai')
                        ->label('Tanggal Selesai')
                        ->date('d F Y')
                        ->placeholder('—'),

                    TextEntry::make('perbaikan.catatan_pbk')
                        ->label('Catatan Perbaikan')
                        ->columnSpanFull()
                        ->default('—'),

                    TextEntry::make('perbaikan.alasan_penolakan')
                        ->label('Alasan Penolakan / Dikembalikan')
                        ->columnSpanFull()
                        ->color('danger')
                        ->visible(fn ($record) => filled($record->perbaikan?->alasan_penolakan)),
                ]),

            // Foto bukti perbaikan dari admin
            Section::make('Foto Bukti Perbaikan')
                ->icon('heroicon-o-camera')
                ->visible(fn ($record) => filled($record->perbaikan?->ft_perbaikan))
                ->schema([
                    ImageEntry::make('perbaikan.ft_perbaikan')
                        ->label('')
                        ->disk('public')
                        ->height(400)
                        ->extraImgAttributes(['style' => 'border-radius:12px;object-fit:cover;width:100%;']),
                ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM — hanya dipakai saat action Setujui / Tolak
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('no_laporan')->disabled(),
            Forms\Components\TextInput::make('nm_pelapor')->disabled(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_laporan')
                    ->label('No. Laporan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tgl_lapor')
                    ->label('Tgl Lapor')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_pelapor')
                    ->label('Pelapor')
                    ->searchable(),

                // Tampilkan lab — fallback ke relasi langsung jika penugasan null
                Tables\Columns\TextColumn::make('lab.nm_lab')
                    ->label('Lab')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('kategori')
                    ->label('Kategori')
                    ->colors(['warning' => 'PC', 'info' => 'non_PC']),

                // Ikon apakah ada foto atau tidak
                Tables\Columns\IconColumn::make('file_foto')
                    ->label('Foto')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->state(fn ($record) => filled($record->file_foto)),

                Tables\Columns\BadgeColumn::make('approval')
                    ->label('Status')
                    ->colors([
                        'warning' => 'menunggu',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                    ]),
            ])
            ->defaultSort('tgl_lapor', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->label('Status')
                    ->options([
                        'menunggu'  => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(['PC' => 'PC', 'non_PC' => 'Non PC']),
                Tables\Filters\Filter::make('ada_foto')
                    ->label('Ada Foto')
                    ->query(fn ($query) => $query->whereNotNull('file_foto')),
            ])
            ->actions([
                // Tombol View — membuka halaman infolist dengan foto
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                // Tombol Setujui
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
                            ->options(function (LaporanKeluhan $record) {
                                return PenugasanUserLab::with(['user', 'lab'])
                                    ->where('status_aktif', 'aktif')
                                    ->whereHas('user', fn ($q) =>
                                        $q->where('role_user', 'admin_lab')
                                    )
                                    ->get()
                                    ->mapWithKeys(fn ($p) => [
                                        $p->id_penugasan =>
                                            $p->user->nm_user . ' — ' . $p->lab->nm_lab
                                    ]);
                            })
                            ->required(),
                    ])
                    ->action(function (LaporanKeluhan $record, array $data) {
                        $record->update([
                            'approval'     => 'disetujui',
                            'id_user'      => Auth::id(),
                            'id_penugasan' => $data['id_penugasan'],
                        ]);
                        Perbaikan::create([
                            'status_perbaikan' => 'antrean',
                            'app_validasi'     => 'menunggu',
                            'id_laporan'       => $record->no_laporan,
                        ]);
                        Notification::make()
                            ->title('Laporan disetujui dan didelegasikan!')
                            ->success()->send();
                    }),

                // Tombol Tolak
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
                            'id_user'  => Auth::id(),
                        ]);
                        Perbaikan::create([
                            'status_perbaikan' => 'selesai',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                            'app_validasi'     => 'divalidasi',
                            'id_laporan'       => $record->no_laporan,
                        ]);
                        Notification::make()
                            ->title('Laporan ditolak.')
                            ->warning()->send();
                    }),
            ]);
    }

    // Filter query berdasarkan role
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['perbaikan', 'penugasan.lab', 'lab']);

        $user = Auth::user();

        if ($user?->isAdminLab()) {
            $labIds = $user->labs()->pluck('id_lab');
            $query->where(function ($q) use ($labIds) {
                $q->whereIn('id_lab', $labIds)
                  ->orWhereHas('penugasan', fn ($q2) =>
                      $q2->whereIn('id_lab', $labIds)
                  );
            });
        } elseif ($user?->isSPV() && !$user->isSPVKedisiplinan()) {
            $labIds = $user->labs()->pluck('id_lab');
            $query->where(function ($q) use ($labIds) {
                $q->whereIn('id_lab', $labIds)
                  ->orWhereHas('penugasan', fn ($q2) =>
                      $q2->whereIn('id_lab', $labIds)
                  );
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLaporanKeluhans::route('/'),
            'view'   => Pages\ViewLaporanKeluhan::route('/{record}'),
        ];
    }
}