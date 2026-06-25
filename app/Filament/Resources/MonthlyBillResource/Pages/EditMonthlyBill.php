<?php

namespace App\Filament\Resources\MonthlyBillResource\Pages;

use App\Filament\Resources\MonthlyBillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyBill extends EditRecord
{
    protected static string $resource = MonthlyBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
