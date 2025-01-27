<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    protected $fillable = [
        'name',
        'expires_at',
        'user_id',
        'private',
        'game_ids',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'private' => 'boolean',
            'game_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function ScopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }
}
