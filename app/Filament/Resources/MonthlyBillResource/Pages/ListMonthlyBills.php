<?php

namespace App\Filament\Resources\MonthlyBillResource\Pages;

use App\Filament\Resources\MonthlyBillResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlyBills extends ListRecords
{
    protected static string $resource = MonthlyBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
