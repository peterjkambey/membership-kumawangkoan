<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyCard extends Model
{
    protected $fillable = [
        'family_no',
        'head_member_id',
        'address',
        'phone',
        'status',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function headMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'head_member_id');
    }

    public function monthlyBills(): HasMany
    {
        return $this->hasMany(MonthlyBill::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFrozen($query)
    {
        return $query->where('status', 'frozen');
    }

    public function getTotalMembersAttribute(): int
    {
        return $this->members()->count();
    }

    public function getOutstandingBillsAttribute()
    {
        return $this->monthlyBills()->whereIn('status', ['unpaid', 'overdue'])->sum('amount');
    }
}
