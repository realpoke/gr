<?php

namespace App\Models\Pivots;

use App\Models\Game;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User;

class GameUserPivot extends Pivot
{
    protected $fillable = [
        'game_id',
        'user_id',
        'elo_change',
        'player_name',
        'eliminated_position',
        'winner',
    ];

    public const FIELDS = [
        'elo_change',
        'player_name',
        'eliminated_position',
        'winner',
    ];

    protected function casts(): array
    {
        return [
            'winner' => 'boolean',
        ];
    }

    public const TABLE = 'game_user_pivot';

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
