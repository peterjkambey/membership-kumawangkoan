<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyCardResource\Pages;
use App\Filament\Resources\FamilyCardResource\RelationManagers\MembersRelationManager;
use App\Models\FamilyCard;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FamilyCardResource extends Resource
{
    protected static ?string $model = FamilyCard::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Kartu Keluarga';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('family_no')
                    ->label('No. KK')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('head_member_id')
                    ->label('Kepala Keluarga')
                    ->relationship('headMember', 'full_name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'frozen' => 'Beku',
                        'deceased' => 'Meninggal',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('family_no')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('headMember.full_name')
                    ->label('Kepala Keluarga')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('members_count')
                    ->label('Jumlah Anggota')
                    ->counts('members')
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'frozen' => 'warning',
                        'deceased' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'frozen' => 'Beku',
                        'deceased' => 'Meninggal',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'frozen' => 'Beku',
                        'deceased' => 'Meninggal',
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
        return [
            MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilyCards::route('/'),
            'create' => Pages\CreateFamilyCard::route('/create'),
            'edit' => Pages\EditFamilyCard::route('/{record}/edit'),
        ];
    }
}
