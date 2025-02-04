<?php

namespace App\Models;

use App\Casts\CastEnumArray;
use App\Enums\Game\GameModeEnum;
use App\Enums\Game\GameTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Map $map) {
            if (empty($map->modes)) {
                $map->modes = [GameModeEnum::UNSUPPORTED];
            }
        });
    }

    protected $fillable = [
        'hash',
        'name',
        'verified_at',
        'file',
        'types',
        'modes',
        'plays',
        'plays_monthly',
        'plays_weekly',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'types' => CastEnumArray::class.':'.GameTypeEnum::class,
        'modes' => CastEnumArray::class.':'.GameModeEnum::class,
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function isRanked(): bool
    {
        return $this->verified_at != null;
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->whereAnyLike([
            'hash',
            'name',
            'id',
        ], $searchTerm);
    }
}
