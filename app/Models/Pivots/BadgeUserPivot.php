<?php

namespace App\Models\Pivots;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BadgeUserPivot extends Pivot
{
    public const TABLE = 'badge_user_pivot';

    protected $fillable = [
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public const FIELDS = [
        'data',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
