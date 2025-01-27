<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('private', true);
    }
}
