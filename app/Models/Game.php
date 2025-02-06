<?php

namespace App\Models;

use App\Enums\Game\GameStatusEnum;
use App\Enums\Game\GameTypeEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Events\PublicGameStatusUpdatedEvent;
use App\Models\Pivots\GameUserPivot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Game extends Model
{
    protected $fillable = [
        'hash',
        'status',
        'type',
        'data',
        'map_id',
        'bracket',
        'elo_average',
    ];

    public function casts(): array
    {
        return [
            'status' => GameStatusEnum::class,
            'type' => GameTypeEnum::class,
            'bracket' => RankBracketEnum::class,
            'data' => 'array',
        ];
    }

    public static function booted(): void
    {
        static::saving(function (Game $game) {
            if ($game->isDirty('status')) {
                broadcast(new PublicGameStatusUpdatedEvent($game->id));
            }
        });
    }

    public function page(): string
    {
        return route('show.game.page', $this->hash);
    }

    public function getWinnersAttribute(): Collection
    {
        return $this->users->where('pivot.winner', true);
    }

    public function getLengthAttribute(): int
    {
        return $this->data['metaData']['gameInterval'] ?? 0;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, GameUserPivot::TABLE)
            ->using(GameUserPivot::class)
            ->withPivot(GameUserPivot::FIELDS)
            ->withTimestamps();
    }

    public function replay(): HasOne
    {
        return $this->hasOne(Replay::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->whereAnyLike([
            'hash',
        ], $searchTerm)->WhereRelation('users', fn ($q) => $q->whereAnyLike([
            'username',
        ], $searchTerm));
    }
}
