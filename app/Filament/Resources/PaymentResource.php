<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static string|\UnitEnum|null $navigationGroup = 'Iuran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('monthly_bill_id')
                    ->label('Tagihan')
                    ->relationship('monthlyBill', 'period', fn ($query) => $query->whereIn('status', ['unpaid', 'overdue']))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->familyCard?->family_no} - {$record->period} (Rp " . number_format($record->amount, 0, ',', '.') . ")"),

                Forms\Components\DatePicker::make('payment_date')
                    ->label('Tanggal Bayar')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),

                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'virtual_account' => 'Virtual Account',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('reference_number')
                    ->label('No. Referensi')
                    ->maxLength(255),

                Forms\Components\Select::make('verified_by')
                    ->label('Diverifikasi Oleh')
                    ->relationship('verifiedBy', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('monthlyBill.familyCard.family_no')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('monthlyBill.period')
                    ->label('Periode')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'virtual_account' => 'VA',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Bayar')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('verifiedBy.name')
                    ->label('Diverifikasi')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'virtual_account' => 'Virtual Account',
                    ]),

                Tables\Filters\Filter::make('payment_date')
                    ->label('Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('payment_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('payment_date', '<=', $date))
                    ),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
