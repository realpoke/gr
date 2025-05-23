<?php

namespace App\Models;

use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankTimeFrameEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $fillable = [
        'game_mode',
        'rank_time_frame',
    ];

    public function casts(): array
    {
        return [
            'game_mode' => GameModeEnum::class,
            'rank_time_frame' => RankTimeFrameEnum::class,
        ];
    }

    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }

    public function scopeFromGameModeAndTimeFrame(
        Builder $query,
        GameModeEnum $gameMode,
        RankTimeFrameEnum $timeFrame
    ): Builder {
        return $query->where('game_mode', $gameMode->value)
            ->where('rank_time_frame', $timeFrame->value);
    }

    public static function scopeAllTimeFromGameMode(
        Builder $query,
        GameModeEnum $gameMode
    ): Builder {
        return $query->fromGameModeAndTimeFrame($gameMode, RankTimeFrameEnum::ALL);
    }

    public static function getFirstOrCreateByGameModeAndTimeFrame(
        GameModeEnum $gameMode,
        RankTimeFrameEnum $timeFrame
    ): self {
        return self::query()->fromGameModeAndTimeFrame($gameMode, $timeFrame)->firstOrCreate([
            'game_mode' => $gameMode,
            'rank_time_frame' => $timeFrame,
        ]);
    }

    public function scopeAllLatestTimeFramesFromGameMode(
        Builder $query,
        GameModeEnum $gameMode
    ): Builder {
        $frames = [
            RankTimeFrameEnum::ALL,
            RankTimeFrameEnum::MONTHLY,
            RankTimeFrameEnum::YEARLY,
        ];

        foreach ($frames as $frame) {
            $query->union(
                $this->getFirstOrCreateByGameModeAndTimeFrame(
                    $gameMode,
                    $frame
                )
            );
        }

        return $query;
    }
}
