<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $label = 'Pengguna';
    protected static ?string $pluralLabel = 'Manajemen User';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPVKedisiplinan() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nm_user')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn ($record) => $record === null)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state)),
            Forms\Components\Select::make('role_user')
                ->label('Role')
                ->options([
                    'spv_kedisiplinan' => 'SPV Kedisiplinan',
                    'spv_jaringan' => 'SPV Jaringan',
                    'spv_inovasi_riset' => 'SPV Inovasi & Riset',
                    'spv_penjadwalan' => 'SPV Penjadwalan',
                    'spv_inventory' => 'SPV Inventory',
                    'spv_keuangan' => 'SPV Keuangan & Surat',
                    'admin_lab' => 'Admin Lab',
                    'asisten_lab' => 'Asisten Lab',
                    'calon_asisten' => 'Calon Asisten',
                ])
                ->required(),
            Forms\Components\Select::make('status_aktif')
                ->label('Status')
                ->options([
                    'aktif' => 'Aktif',
                    'nonaktif' => 'Non Aktif',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nm_user')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\BadgeColumn::make('role_user')
                    ->label('Role')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'spv_kedisiplinan' => 'SPV Kedisiplinan',
                        'spv_jaringan' => 'SPV Jaringan',
                        'spv_inovasi_riset' => 'SPV Inovasi & Riset',
                        'spv_penjadwalan' => 'SPV Penjadwalan',
                        'spv_inventory' => 'SPV Inventory',
                        'spv_keuangan' => 'SPV Keuangan',
                        'admin_lab' => 'Admin Lab',
                        'asisten_lab' => 'Asisten Lab',
                        'calon_asisten' => 'Calon Asisten',
                        default => $state,
                    })
                    ->colors([
                        'danger' => fn ($state) => str_starts_with($state, 'spv_'),
                        'warning' => 'admin_lab',
                        'success' => 'asisten_lab',
                        'gray' => 'calon_asisten',
                    ]),
                Tables\Columns\BadgeColumn::make('status_aktif')
                    ->label('Status')
                    ->colors(['success' => 'aktif', 'danger' => 'nonaktif']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_user')
                    ->label('Role')
                    ->options([
                        'spv_kedisiplinan' => 'SPV Kedisiplinan',
                        'spv_jaringan' => 'SPV Jaringan',
                        'admin_lab' => 'Admin Lab',
                        'asisten_lab' => 'Asisten Lab',
                    ]),
                Tables\Filters\SelectFilter::make('status_aktif')
                    ->label('Status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Non Aktif']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}