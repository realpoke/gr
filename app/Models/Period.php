<?php

namespace App\Models;

use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankTimeFrameEnum;
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
}
