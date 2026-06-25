<?php

namespace App\Filament\Widgets;

use App\Models\MonthlyBill;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingBillsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MonthlyBill::whereIn('status', ['unpaid', 'overdue'])
                    ->where('due_date', '<=', now()->addMonth())
                    ->orderBy('due_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('familyCard.family_no')
                    ->label('No. KK')
                    ->searchable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'unpaid' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'unpaid' => 'Belum Dibayar',
                        'overdue' => 'Terlambat',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('familyCard.headMember.full_name')
                    ->label('Kepala Keluarga'),
            ]);
    }
}
