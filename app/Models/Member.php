<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'full_name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'photo',
        'family_card_id',
        'region_id',
        'membership_number',
        'join_date',
        'family_role',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($member) {
            if (empty($member->membership_number)) {
                $regionCode = $member->region?->code ?? 'XX';
                $year = now()->format('Y');

                $latest = Member::where('membership_number', 'like', "KMN/{$regionCode}/{$year}/%")
                    ->lockForUpdate()
                    ->orderBy('membership_number', 'desc')
                    ->first();

                $sequence = 1;
                if ($latest) {
                    $parts = explode('/', $latest->membership_number);
                    if (count($parts) === 4) {
                        $sequence = intval($parts[3]) + 1;
                    }
                }

                $member->membership_number = sprintf(
                    'KMN/%s/%s/%04d',
                    $regionCode,
                    $year,
                    $sequence
                );
            }
        });

        static::updated(function ($member) {
            if ($member->wasChanged('status')) {
                $member->statusLogs()->create([
                    'previous_status' => $member->getOriginal('status'),
                    'new_status' => $member->status,
                    'reason' => 'Status changed during member update.',
                    'changed_by' => auth()->id(),
                ]);
            }
        });
    }

    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function supportBodies(): BelongsToMany
    {
        return $this->belongsToMany(SupportBody::class, 'support_body_member')
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(MemberMembership::class);
    }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(MemberMembership::class)->where('status', 'active');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MembershipStatusLog::class);
    }

    public function headOfFamilyCards(): HasMany
    {
        return $this->hasMany(FamilyCard::class, 'head_member_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->age;
    }

    public function getFamilyRoleLabelAttribute(): string
    {
        return match($this->family_role) {
            'head' => 'Kepala Keluarga',
            'spouse' => 'Pasangan',
            'child' => 'Anak',
            'parent' => 'Orang Tua',
            'sibling' => 'Saudara',
            default => 'Lainnya',
        };
    }
}
