<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function familyCards(): HasMany
    {
        return $this->hasManyThrough(FamilyCard::class, Member::class, 'region_id', 'id', 'id', 'family_card_id');
    }
}
