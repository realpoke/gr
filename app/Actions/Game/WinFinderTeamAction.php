<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Contracts\WinFinderContract;
use App\Models\Game;

class WinFinderTeamAction extends BaseAction implements WinFinderContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $finder = $this->findAndSetWinner($this->game);

        if (! is_null($finder)) {
            return $this->setFailed($finder);
        }

        $this->game = $this->game->refresh();

        return $this->setSuccessful();
    }

    private function findAndSetWinner(Game $game): ?string
    {
        $winningTeam = null;

        $data = $game->data;
        $importantOrders = collect($data['importantOrders'] ?? []);
        $playersPlaying = $data['metaData']['playersPlaying'];

        // 1. Group players by team (pluck player names)
        $teamPlayerNames = collect($data['players'] ?? [])
            ->filter(fn ($player) => $player['isPlaying'])
            ->groupBy('team')
            ->map(fn ($group) => $group->pluck('name')->toArray())
            ->toArray();

        // 2. Get players' latest surrender or lastOrder time
        $playerLastActions = collect($data['players'] ?? [])
            ->filter(fn ($player) => $player['isPlaying'])
            ->mapWithKeys(function ($player) use ($importantOrders) {
                $surrender = $importantOrders
                    ->where('OrderName', 'Surrender')
                    ->where('PlayerName', $player['name'])
                    ->sortBy('TimeCode')
                    ->last();

                return [$player['name'] => $surrender['TimeCode'] ?? $player['lastOrder']['TimeCode']];
            });

        // If all players have a last action recorded
        if ($playersPlaying == $playerLastActions->count()) {
            // Determine the last action
            $lastSurrender = $playerLastActions->sort()->last();

            // Determine which team the last surrendering player belongs to
            foreach ($teamPlayerNames as $teamId => $players) {
                if (array_search($lastSurrender, $playerLastActions->toArray(), true) !== false) {
                    $winningTeam = $teamId;
                    break;
                }
            }
        }

        if (is_null($winningTeam)) {
            return 'Could not determine winning team in game: '.$game->id;
        }

        // Mark win flags on players: winning team gets true, others false
        $dataPlayers = collect($game->data['players'])->map(function ($player) use ($winningTeam) {
            $player['win'] = ($player['team'] == $winningTeam);

            return $player;
        })->toArray();

        $data['players'] = $dataPlayers;
        $game->data = $data;

        if (! $game->save()) {
            return 'Failed to save game data: '.$game->id;
        }

        return null;
    }
}
