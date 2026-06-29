<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupportBody extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'support_body_member')
            ->withTimestamps();
    }
}
