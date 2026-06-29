<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberMembershipResource\Pages;
use App\Models\MemberMembership;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberMembershipResource extends Resource
{
    protected static ?string $model = MemberMembership::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Riwayat Keanggotaan';

    protected static string|\UnitEnum|null $navigationGroup = 'Keanggotaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->label('Anggota')
                    ->relationship('member', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('membership_number')
                    ->label('No. Anggota')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Berakhir'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('membership_number')
                    ->label('No. Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberMemberships::route('/'),
            'create' => Pages\CreateMemberMembership::route('/create'),
            'edit' => Pages\EditMemberMembership::route('/{record}/edit'),
        ];
    }
}
