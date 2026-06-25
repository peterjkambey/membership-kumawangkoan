<?php

namespace App\Filament\Resources\FamilyCardResource\Pages;

use App\Filament\Resources\FamilyCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamilyCard extends EditRecord
{
    protected static string $resource = FamilyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
