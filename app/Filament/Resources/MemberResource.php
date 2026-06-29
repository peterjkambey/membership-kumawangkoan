<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers\BenefitsRelationManager;
use App\Models\Member;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Anggota';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('member-photos')
                            ->visibility('public'),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Data Keanggotaan')
                    ->schema([
                        Forms\Components\Select::make('family_card_id')
                            ->label('Kartu Keluarga')
                            ->relationship('familyCard', 'family_no')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('family_role')
                            ->label('Peran dalam Keluarga')
                            ->options([
                                'head' => 'Kepala Keluarga',
                                'spouse' => 'Pasangan',
                                'child' => 'Anak',
                                'parent' => 'Orang Tua',
                                'sibling' => 'Saudara',
                                'other' => 'Lainnya',
                            ])
                            ->default('other'),

                        Forms\Components\Select::make('region_id')
                            ->label('Wilayah')
                            ->relationship('region', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('membership_number')
                            ->label('No. Anggota')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\DatePicker::make('join_date')
                            ->label('Tanggal Bergabung'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'deceased' => 'Meninggal',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Kartu E-Toll')
                    ->schema([
                        Forms\Components\TextInput::make('card_uid')
                            ->label('UID Kartu E-Toll')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Nomor unik kartu e-toll dari vendor'),

                        Forms\Components\DatePicker::make('card_issued_at')
                            ->label('Tanggal Terbit Kartu'),

                        Forms\Components\Select::make('card_status')
                            ->label('Status Kartu')
                            ->options([
                                'none' => 'Belum Ada',
                                'issued' => 'Sudah Terbit',
                                'lost' => 'Hilang',
                                'replaced' => 'Diganti',
                            ])
                            ->default('none'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('JK')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'L' : 'P')
                    ->sortable(),

                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Usia')
                    ->formatStateUsing(fn ($state) => $state?->age . ' thn')
                    ->sortable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->label('Wilayah')
                    ->sortable(),

                Tables\Columns\TextColumn::make('familyCard.family_no')
                    ->label('No. KK')
                    ->sortable(),

                Tables\Columns\TextColumn::make('card_uid')
                    ->label('Kartu E-Toll')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('card_status')
                    ->label('Status Kartu')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'issued' => 'success',
                        'lost' => 'danger',
                        'replaced' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'issued' => 'Terbit',
                        'lost' => 'Hilang',
                        'replaced' => 'Diganti',
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    })
                    ->toggleable(),

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
            ->filters([
                SelectFilter::make('region_id')
                    ->label('Wilayah')
                    ->relationship('region', 'name'),

                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'deceased' => 'Meninggal',
                    ]),

                SelectFilter::make('family_role')
                    ->label('Peran Keluarga')
                    ->options([
                        'head' => 'Kepala Keluarga',
                        'spouse' => 'Pasangan',
                        'child' => 'Anak',
                        'parent' => 'Orang Tua',
                        'sibling' => 'Saudara',
                        'other' => 'Lainnya',
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
            BenefitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
