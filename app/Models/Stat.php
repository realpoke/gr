<?php

namespace App\Models;

use App\Enums\FactionEnum;
use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Enums\Rank\RankTimeFrameEnum;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Workbench\App\Models\User;

class Stat extends Model
{
    protected $fillable = [
        'user_id',
        'period_id',
        'data',
        'last_game_at',
        'bracket',
        'favorite_faction',
        'elo',
        'rank',
        'win_percentage',
        'wins',
        'losses',
        'games',
        'streak',
    ];

    protected static function booted(): void
    {
        static::creating(function (Stat $stat) {
            $stat->last_game_at = now();
        });
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'game_mode' => GameModeEnum::class,
            'rank_time_frame' => RankTimeFrameEnum::class,
            'bracket' => RankBracketEnum::class,
            'favorite_faction' => FactionEnum::class,
            'last_game_at' => 'datetime',
            'win_percentage' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function scopeForPeriod(Builder $query, Period $period): Builder
    {
        return $query->where('period_id', $period->id);
    }

    public function getProfileUrlAttribute(): string
    {
        return asset('storage/images/brackets/profile/'.$this->bracket->value.'_'.$this->favorite_faction->value.'.png');
    }

    public function getBadgeUrlAttribute(): string
    {
        return asset('storage/images/brackets/badge/'.$this->bracket->value.'_'.$this->favorite_faction->value.'.png');
    }

    public function favoriteBaseFaction(): FactionEnum
    {
        if (empty($this->data['factions'] ?? [])) {
            return FactionEnum::RANDOM;
        }

        $baseFactions = [
            FactionEnum::USA->value => 0,
            FactionEnum::CHINA->value => 0,
            FactionEnum::GLA->value => 0,
            FactionEnum::RANDOM->value => 0,
        ];

        foreach ($this->data['factions'] as $faction => $games) {
            $factionEnum = FactionEnum::tryFrom($faction);
            if ($factionEnum && $factionEnum->isPlaying()) {
                $baseFaction = $factionEnum->baseFaction()->value;
                $baseFactions[$baseFaction] += $games;
            }
        }

        if (empty($baseFactions)) {
            return FactionEnum::RANDOM;
        }

        arsort($baseFactions);
        $factions = array_keys($baseFactions);

        return FactionEnum::tryFrom($factions[0]);
    }

    public function giveStats(Collection $statsToAdd): bool
    {
        $this->lockForUpdate();

        $strippedStats = $statsToAdd->only([
            'faction',
            'side',
            'moneySpent',
            'unitsCreated',
            'buildingsBuilt',
            'upgradesBuilt',
            'powersUsed',
        ]);

        $currentData = $this->data ?? [];

        if (empty($currentData)) {
            // Initialize the stats structure
            $currentData = [
                'sides' => [
                    $strippedStats['side'] => 1,
                ],
                'factions' => [
                    $strippedStats['faction'] => 1,
                ],
            ];

            foreach ($strippedStats->except('side', 'faction') as $key => $value) {
                $currentData[$key] = $value;
            }
        } else {
            $side = $strippedStats['side'];
            $currentData['sides'][$side] = ($currentData['sides'][$side] ?? 0) + 1;
            $faction = $strippedStats['faction'];
            $currentData['factions'][$faction] = ($currentData['factions'][$faction] ?? 0) + 1;

            foreach ($strippedStats->except('side', 'faction') as $key => $value) {
                if (is_array($value) && isset($currentData[$key]) && is_array($currentData[$key])) {
                    $currentData[$key] = $this->mergeStats($currentData[$key], $value);
                } elseif (is_numeric($value)) {
                    $currentData[$key] = ($currentData[$key] ?? 0) + $value;
                } else {
                    $currentData[$key] = $value;
                }
            }
        }

        $this->data = $currentData;
        if (! $this->save()) {
            throw new \RuntimeException("Failed to save stats for stat: {$this->id}");
        }

        return true;
    }

    private function mergeStats(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (is_array($value) && isset($existing[$key]) && is_array($existing[$key])) {
                $existing[$key] = $this->mergeStats($existing[$key], $value);
            } elseif (is_numeric($value)) {
                $existing[$key] = ($existing[$key] ?? 0) + $value;
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}
