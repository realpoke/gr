<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Contracts\WinFinderContract;
use App\Models\Game;

class WinFinderFreeForAllAction extends BaseAction implements WinFinderContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $this->findAndSetWinnerAndOrder($this->game);

        $this->game = $this->game->refresh();

        return $this->setSuccessful();
    }

    private function findAndSetWinnerAndOrder(Game $game): void
    {
        $data = $game->data;
        $importantOrders = $data['importantOrders'];
        $playingPlayers = collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying'])
            ->toArray();

        $eliminationOrder = [];
        $winningPlayerName = null;

        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'Surrender') {
                $eliminationOrder[] = $order['PlayerName'];
            }
        }

        // Find the winner (last EndReplay among non-surrendered players)
        $lastEndReplay = null;
        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'EndReplay' && ! in_array($order['PlayerName'], $eliminationOrder)) {
                if (! $lastEndReplay || $order['TimeCode'] > $lastEndReplay['TimeCode']) {
                    $lastEndReplay = $order;
                }
            }
        }

        if ($lastEndReplay) {
            $winningPlayerName = $lastEndReplay['PlayerName'];
        }

        // Update winners in game data
        foreach ($playingPlayers as $key => $player) {
            $playingPlayers[$key]['win'] = $player['name'] === $winningPlayerName;
        }

        $data['players'] = $playingPlayers;
        $game->data = $data;
        $game->save();

        // Assign positions
        $totalPlayers = count($playingPlayers);
        $positions = [];

        // Winner gets highest position
        if ($winningPlayerName) {
            $positions[$winningPlayerName] = $totalPlayers;
        }

        // Eliminated players get positions in reverse order of elimination
        $currentPosition = $totalPlayers - 1;
        foreach ($eliminationOrder as $playerName) {
            $positions[$playerName] = $currentPosition;
            $currentPosition--;
        }

        // Update all player positions in one go
        foreach ($positions as $playerName => $position) {
            $game->users()->wherePivot('player_name', $playerName)
                ->updateExistingPivot(
                    $game->users()->wherePivot('player_name', $playerName)->first()->id,
                    ['eliminated_position' => $position]
                );
        }
    }
}
