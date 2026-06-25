<?php

namespace App\Filament\Resources\FamilyCardResource\Pages;

use App\Filament\Resources\FamilyCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFamilyCards extends ListRecords
{
    protected static string $resource = FamilyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
