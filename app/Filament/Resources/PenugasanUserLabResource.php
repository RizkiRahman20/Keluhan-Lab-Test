<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenugasanUserLabResource\Pages;
use App\Models\Lab;
use App\Models\PenugasanUserLab;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PenugasanUserLabResource extends Resource
{
    protected static ?string $model = PenugasanUserLab::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $label = 'Penugasan';
    protected static ?string $pluralLabel = 'Penugasan User ke Lab';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPVKedisiplinan() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('id_user')
                ->label('User / Asisten')
                ->options(User::where('status_aktif', 'aktif')->pluck('nm_user', 'id_user'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('id_lab')
                ->label('Laboratorium')
                ->options(Lab::where('status_lab', 'aktif')->pluck('nm_lab', 'id_lab'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('semester')
                ->label('Semester')
                ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                ->required(),
            Forms\Components\TextInput::make('tahun_ajaran')
                ->label('Tahun Ajaran')
                ->placeholder('2024/2025')
                ->required(),
            Forms\Components\Select::make('status_aktif')
                ->label('Status')
                ->options(['aktif' => 'Aktif', 'nonaktif' => 'Non Aktif'])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nm_user')->label('Nama User')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.role_user')->label('Role'),
                Tables\Columns\TextColumn::make('lab.nm_lab')->label('Lab')->searchable(),
                Tables\Columns\TextColumn::make('semester')->label('Semester'),
                Tables\Columns\TextColumn::make('tahun_ajaran')->label('Tahun Ajaran'),
                Tables\Columns\BadgeColumn::make('status_aktif')
                    ->label('Status')
                    ->colors(['success' => 'aktif', 'danger' => 'nonaktif']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Lab')
                    ->relationship('lab', 'nm_lab'),
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
            'index' => Pages\ListPenugasanUserLabs::route('/'),
            'create' => Pages\CreatePenugasanUserLab::route('/create'),
            'edit' => Pages\EditPenugasanUserLab::route('/{record}/edit'),
        ];
    }
}