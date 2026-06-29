<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGatewayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'channel',
        'external_id',
        'gateway_transaction_id',
        'family_card_id',
        'amount',
        'paid_amount',
        'status',
        'bank_code',
        'account_number',
        'qr_string',
        'gateway_response',
        'expires_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'gateway_response' => 'json',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'PAID');
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'PENDING';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'PAID';
    }
}
