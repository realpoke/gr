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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

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

        $periods = Period::allLatestTimeFramesFromGameMode($game->type->getGameMode())->get();

        DB::beginTransaction();
        foreach ($game->users as $user) {

            foreach ($periods as $period) {

                $stat = $user->getOrCreateCurrentStatsForPeriod($period);

                $gameAdder = new StatAddGamePlayedAction($stat, $game, $user);
                $gameAdder->handle();

                if ($gameAdder->failed()) {
                    $this->job->fail($gameAdder->getErrorMessage());

                    DB::rollBack();

                    return;
                }

                $statAdder = new StatAddGameStatsAction($stat, $game, $user);
                $statAdder->handle();

                if ($statAdder->failed()) {
                    $this->job->fail($statAdder->getErrorMessage());

                    DB::rollBack();

                    return;
                }

                $eloChanger = new StatAddGameEloAction($stat, $user);
                $eloChanger->handle();

                if ($eloChanger->failed()) {
                    $this->job->fail($eloChanger->getErrorMessage());

                    DB::rollBack();

                    return;
                }

                $bracketUpdater = new UpdateStatRankBracketAction($stat);
                $bracketUpdater->handle();

                if ($bracketUpdater->failed()) {
                    $this->job->fail($bracketUpdater->getErrorMessage());

                    DB::rollBack();

                    return;
                }

                $factionUpdater = new UpdateStatFavoriteFactionAction($stat, $game, $user);
                $factionUpdater->handle();

                if ($factionUpdater->failed()) {
                    $this->job->fail($factionUpdater->getErrorMessage());

                    DB::rollBack();

                    return;
                }
            }

        }

        DB::commit();
    }

    private function isRankedGame(Game $game): bool
    {
        return $game->status === GameStatusEnum::RANKED;
    }
}
