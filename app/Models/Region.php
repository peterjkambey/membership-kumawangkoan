<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function familyCards(): HasManyThrough
    {
        return $this->hasManyThrough(FamilyCard::class, Member::class, 'region_id', 'id', 'id', 'family_card_id');
    }
}
