<?php

namespace App\Models;

use App\Models\Pivots\GameUserPivot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gentool extends Model
{
    protected $fillable = [
        'gentool_id',
        'private',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plays(): HasMany
    {
        return $this->hasMany(GameUserPivot::class);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('private', true);
    }
}
