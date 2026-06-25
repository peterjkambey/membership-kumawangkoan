<?php

namespace App\Filament\Resources\SupportBodyResource\Pages;

use App\Filament\Resources\SupportBodyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupportBody extends EditRecord
{
    protected static string $resource = SupportBodyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
