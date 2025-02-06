<?php

namespace App\Jobs;

use App\Actions\Game\SetGameAverageEloAction;
use App\Actions\Game\SetGamePivotWinnerAction;
use App\Actions\Game\SetGameRankedAction;
use App\Actions\Stat\StatAddGameEloAction;
use App\Actions\Stat\StatAddGamePlayedAction;
use App\Actions\Stat\StatAddGameStatsAction;
use App\Actions\Stat\UpdateStatFavoriteFactionAction;
use App\Actions\Stat\UpdateStatRankBracketAction;
use App\Enums\Game\GameStatusEnum;
use App\Factories\EloCalculatorFactory;
use App\Factories\WinFinderFactory;
use App\Models\Game;
use App\Models\Period;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

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
        $game = Game::findOrFail($this->gameId);

        $rankedSetter = new SetGameRankedAction($game);
        $rankedSetter->handle();

        if ($rankedSetter->failed()) {
            $this->job->fail($rankedSetter->getErrorMessage());

            return;
        }

        $winFinder = app(WinFinderFactory::class)($game);
        if (is_null($winFinder)) {
            $this->job->fail('Failed to find win finder for game: '.$game->id);

            return;
        }

        $winFinder->handle();
        if ($winFinder->failed()) {
            $this->job->fail($winFinder->getErrorMessage());

            return;
        }
        $game = $winFinder->getGame();

        $winSetter = new SetGamePivotWinnerAction($game);
        $winSetter->handle();

        if ($winSetter->failed()) {
            $this->job->fail($winSetter->getErrorMessage());

            return;
        }

        if (! $this->isRankedGame($game)) {
            $game->status = GameStatusEnum::UNRANKED;

            return;
        }

        $eloCalculator = app(EloCalculatorFactory::class)($game);
        if (is_null($eloCalculator)) {
            $this->job->fail('Failed to find elo calculator for game: '.$game->id);

            return;
        }

        $eloCalculator->handle();
        if ($eloCalculator->failed()) {
            $this->job->fail($eloCalculator->getErrorMessage());

            return;
        }

        $averageEloSetter = new SetGameAverageEloAction($game);
        $averageEloSetter->handle();

        if ($averageEloSetter->failed()) {
            $this->job->fail($averageEloSetter->getErrorMessage());

            return;
        }

        $game->refresh();

        try {
            DB::transaction(function () use ($game) {
                $periods = Period::allLatestTimeFramesFromGameMode($game->type->getGameMode())->get();

                foreach ($game->users as $user) {

                    foreach ($periods as $period) {

                        $stat = $user->getOrCreateCurrentStatsForPeriod($period);

                        $gameAdder = new StatAddGamePlayedAction($stat, $game, $user);
                        $gameAdder->handle();

                        if ($gameAdder->failed()) {
                            throw new RuntimeException($gameAdder->getErrorMessage());
                        }

                        $statAdder = new StatAddGameStatsAction($stat, $game, $user);
                        $statAdder->handle();

                        if ($statAdder->failed()) {
                            throw new RuntimeException($statAdder->getErrorMessage());
                        }

                        $eloChanger = new StatAddGameEloAction($stat, $user);
                        $eloChanger->handle();

                        if ($eloChanger->failed()) {
                            throw new RuntimeException($eloChanger->getErrorMessage());
                        }

                        $bracketUpdater = new UpdateStatRankBracketAction($stat);
                        $bracketUpdater->handle();

                        if ($bracketUpdater->failed()) {
                            throw new RuntimeException($bracketUpdater->getErrorMessage());
                        }

                        $factionUpdater = new UpdateStatFavoriteFactionAction($stat, $game, $user);
                        $factionUpdater->handle();

                        if ($factionUpdater->failed()) {
                            throw new RuntimeException($factionUpdater->getErrorMessage());
                        }
                    }

                }
            });
        } catch (Exception $e) {
            Log::error('Failed to process game stats for game ID: '.$game->id.'. Error: '.$e->getMessage());

            return;
        }
    }

    private function isRankedGame(Game $game): bool
    {
        return $game->status === GameStatusEnum::RANKED;
    }
}
