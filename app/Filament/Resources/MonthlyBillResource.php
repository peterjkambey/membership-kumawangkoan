<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyBillResource\Pages;
use App\Models\MonthlyBill;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class MonthlyBillResource extends Resource
{
    protected static ?string $model = MonthlyBill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Tagihan Bulanan';

    protected static string|\UnitEnum|null $navigationGroup = 'Iuran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('family_card_id')
                    ->label('Kartu Keluarga')
                    ->relationship('familyCard', 'family_no')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('period')
                    ->label('Periode')
                    ->placeholder('YYYY-MM')
                    ->required()
                    ->maxLength(7),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Lunas',
                        'overdue' => 'Terlambat',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('familyCard.family_no')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'unpaid',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Lunas',
                        'overdue' => 'Terlambat',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Pembayaran')
                    ->counts('payments')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Periode'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Lunas',
                        'overdue' => 'Terlambat',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyBills::route('/'),
            'create' => Pages\CreateMonthlyBill::route('/create'),
            'edit' => Pages\EditMonthlyBill::route('/{record}/edit'),
        ];
    }
}
