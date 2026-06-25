<?php

namespace App\Filament\Resources\SupportBodyResource\Pages;

use App\Filament\Resources\SupportBodyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupportBodies extends ListRecords
{
    protected static string $resource = SupportBodyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
