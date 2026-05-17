<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabResource\Pages;
use App\Models\Lab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LabResource extends Resource
{
    protected static ?string $model = Lab::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $label = 'Laboratorium';
    protected static ?string $pluralLabel = 'Manajemen Lab';
    protected static ?int $navigationSort = 1;

    // Hanya SPV Kedisiplinan yang bisa akses
    public static function canViewAny(): bool
    {
        return Auth::user()?->hasRole(['spv_kedisiplinan', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kd_lab')
                ->label('Kode Lab')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('nm_lab')
                ->label('Nama Lab')
                ->required()
                ->maxLength(100),
            Forms\Components\Select::make('status_lab')
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
                Tables\Columns\TextColumn::make('kd_lab')->label('Kode Lab')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nm_lab')->label('Nama Lab')->searchable(),
                Tables\Columns\BadgeColumn::make('status_lab')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_lab')
                    ->label('Status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Non Aktif']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabs::route('/'),
            'create' => Pages\CreateLab::route('/create'),
            'edit' => Pages\EditLab::route('/{record}/edit'),
        ];
    }
}