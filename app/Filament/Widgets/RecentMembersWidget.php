<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentMembersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->label('Wilayah'),

                Tables\Columns\TextColumn::make('familyCard.family_no')
                    ->label('No. KK'),

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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime(),
            ]);
    }
}
