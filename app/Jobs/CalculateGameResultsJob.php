<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateGameResultsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $gameId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO FINISH THIS
        $game = Game::findOrFail($this->gameId);
    }
}
