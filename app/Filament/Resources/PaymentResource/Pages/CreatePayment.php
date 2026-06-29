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

        // Jika bayar per tagihan spesifik, update status bill
        if ($record->monthly_bill_id) {
            // Pastikan family_card_id terisi dari bill untuk scoping
            if (!$record->family_card_id) {
                $bill = \App\Models\MonthlyBill::find($record->monthly_bill_id);
                if ($bill && $bill->family_card_id) {
                    $record->family_card_id = $bill->family_card_id;
                    $record->saveQuietly();
                }
            }
            $this->updateBillStatus($record->monthly_bill_id);
        }
    }

    /**
     * Update status monthly bill jika total payment >= amount.
     */
    private function updateBillStatus(int $monthlyBillId): void
    {
        $bill = \App\Models\MonthlyBill::find($monthlyBillId);
        if (!$bill) {
            return;
        }

        $totalPaid = \App\Models\Payment::where('monthly_bill_id', $bill->id)->sum('amount');
        if ($totalPaid >= (float) $bill->amount) {
            $bill->update(['status' => 'paid']);
        }
    }
}
