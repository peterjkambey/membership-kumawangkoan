<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Services\PaymentAllocator;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Jika bayar per KK (tanpa bill spesifik), alokasikan otomatis
        if ($record->family_card_id && !$record->monthly_bill_id) {
            $allocator = app(PaymentAllocator::class);
            $card = $record->familyCard;

            $result = $allocator->allocate($card, (float) $record->amount, [
                'payment_date' => $record->payment_date,
                'payment_method' => $record->payment_method,
                'reference_number' => $record->reference_number,
                'verified_by' => $record->verified_by,
                'notes' => $record->notes,
            ]);

            // Hapus record awal (cuma sebagai trigger), sudah diganti oleh allocator
            $record->delete();

            Notification::make()
                ->title($result['message'])
                ->body("Dialokasikan ke {$result['allocated_bills']} tagihan. Sisa: Rp " . number_format($result['remaining'], 0, ',', '.'))
                ->success()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }
    }
}
