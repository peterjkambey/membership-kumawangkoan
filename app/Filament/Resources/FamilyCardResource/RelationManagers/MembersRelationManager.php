<?php

namespace App\Filament\Resources\FamilyCardResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Anggota Keluarga';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('full_name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                \Filament\Forms\Components\Select::make('family_role')
                    ->label('Peran dalam Keluarga')
                    ->options([
                        'head' => 'Kepala Keluarga',
                        'spouse' => 'Pasangan',
                        'child' => 'Anak',
                        'parent' => 'Orang Tua',
                        'sibling' => 'Saudara',
                        'other' => 'Lainnya',
                    ])
                    ->required(),

                \Filament\Forms\Components\Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),

                \Filament\Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'deceased' => 'Meninggal',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('family_role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'head' => 'Kepala Keluarga',
                        'spouse' => 'Pasangan',
                        'child' => 'Anak',
                        'parent' => 'Orang Tua',
                        'sibling' => 'Saudara',
                        default => 'Lainnya',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'deceased' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'deceased' => 'Meninggal',
                        default => $state,
                    }),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
