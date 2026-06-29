<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_bill_id',
        'family_card_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (empty($payment->reference_number)) {
                $date = $payment->payment_date ?? now();
                $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Ymd') : date('Ymd', strtotime($date));

                $latest = Payment::where('reference_number', 'like', "PAY/{$dateStr}/%")
                    ->lockForUpdate()
                    ->orderBy('reference_number', 'desc')
                    ->first();

                $sequence = 1;
                if ($latest) {
                    $parts = explode('/', $latest->reference_number);
                    if (count($parts) === 3) {
                        $sequence = intval($parts[2]) + 1;
                    }
                }

                $payment->reference_number = sprintf(
                    'PAY/%s/%04d',
                    $dateStr,
                    $sequence
                );
            }
        });
    }

    public function monthlyBill(): BelongsTo
    {
        return $this->belongsTo(MonthlyBill::class);
    }

    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
