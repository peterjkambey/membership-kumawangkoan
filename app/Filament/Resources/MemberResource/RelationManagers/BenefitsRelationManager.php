<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BenefitsRelationManager extends RelationManager
{
    protected static string $relationship = 'benefits';

    protected static ?string $title = 'Hak & Benefit';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('benefit_id')
                    ->label('Benefit')
                    ->options(fn () => \App\Models\Benefit::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'eligible' => 'Berhak',
                        'granted' => 'Sudah Diberikan',
                        'used' => 'Sudah Digunakan',
                        'expired' => 'Kadaluarsa',
                    ])
                    ->default('eligible')
                    ->required(),

                Forms\Components\DatePicker::make('granted_at')
                    ->label('Tanggal Diberikan'),

                Forms\Components\DatePicker::make('expires_at')
                    ->label('Berlaku Sampai'),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Benefit'),

                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'granted' => 'success',
                        'eligible' => 'info',
                        'used' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'eligible' => 'Berhak',
                        'granted' => 'Diberikan',
                        'used' => 'Digunakan',
                        'expired' => 'Kadaluarsa',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('pivot.granted_at')
                    ->label('Diberikan')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.expires_at')
                    ->label('Berakhir')
                    ->date()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Benefit')
                    ->modalHeading('Tambah Benefit ke Anggota'),
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
