<?php

namespace App\Services;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentAllocator
{
    /**
     * Alokasikan pembayaran KK ke tagihan terlama.
     * Satu payment bisa cover multiple bulan.
     *
     * @return array ['allocated_bills' => int, 'remaining' => float, 'excess' => float]
     */
    public function allocate(FamilyCard $card, float $amount, array $paymentData = []): array
    {
        $unpaidBills = MonthlyBill::where('family_card_id', $card->id)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderBy('period')
            ->get();

        if ($unpaidBills->isEmpty()) {
            // Tidak ada tagihan — simpan sebagai kelebihan/credit
            return [
                'allocated_bills' => 0,
                'remaining' => $amount,
                'excess' => $amount,
                'message' => 'Tidak ada tagihan yang perlu dibayar.',
            ];
        }

        $totalDue = $unpaidBills->sum('amount');
        $remainingAmount = $amount;
        $allocatedCount = 0;

        DB::transaction(function () use ($unpaidBills, $card, $paymentData, &$remainingAmount, &$allocatedCount) {
            foreach ($unpaidBills as $bill) {
                if ($remainingAmount <= 0) {
                    break;
                }

                if (!in_array($bill->status, ['unpaid', 'overdue'])) {
                    continue;
                }

                // Hitung sisa tagihan
                $paidSoFar = $bill->payments()->sum('amount');
                $billRemaining = max(0, (float) $bill->amount - $paidSoFar);

                if ($billRemaining <= 0) {
                    continue;
                }

                $payAmount = min($remainingAmount, $billRemaining);

                // Simpan payment untuk bill ini
                Payment::create(array_merge($paymentData, [
                    'monthly_bill_id' => $bill->id,
                    'family_card_id' => $card->id,
                    'amount' => $payAmount,
                ]));

                $remainingAmount -= $payAmount;
                $allocatedCount++;

                // Update status bill
                $totalPaid = Payment::where('monthly_bill_id', $bill->id)->sum('amount');
                if ($totalPaid >= (float) $bill->amount) {
                    $bill->update(['status' => 'paid']);
                }
            }
        });

        return [
            'allocated_bills' => $allocatedCount,
            'remaining' => max(0, $remainingAmount),
            'excess' => max(0, $remainingAmount),
            'message' => $allocatedCount > 0
                ? "Pembayaran dialokasikan ke {$allocatedCount} tagihan."
                : 'Tidak ada tagihan yang bisa dibayar.',
        ];
    }
}
