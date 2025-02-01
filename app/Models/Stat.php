<?php

namespace App\Models;

use App\Enums\FactionEnum;
use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Enums\Rank\RankTimeFrameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workbench\App\Models\User;

class Stat extends Model
{
    protected $fillable = [
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
}
