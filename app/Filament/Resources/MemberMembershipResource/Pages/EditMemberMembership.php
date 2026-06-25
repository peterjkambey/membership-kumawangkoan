<?php

namespace App\Filament\Resources\MemberMembershipResource\Pages;

use App\Filament\Resources\MemberMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberMembership extends EditRecord
{
    protected static string $resource = MemberMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
