<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Contracts\WinFinderContract;
use App\Models\Game;

class WinFinderOneOnOneAction extends BaseAction implements WinFinderContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $playingPlayers = collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying'])
            ->toArray();

        $winnerCount = 0;
        foreach ($playingPlayers as $player) {
            if ($player['win']) {
                $winnerCount++;
            }
        }

        if ($winnerCount == 0 || $winnerCount == 2) {
            $this->findAndSetWinner($this->game);
        }

        $this->game = $this->game->refresh();

        return $this->setSuccessful();
    }

    private function findAndSetWinner(Game $game): void
    {
        $data = $game->data;
        $importantOrders = $data['importantOrders'];
        $playingPlayers = collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying'])
            ->toArray();

        foreach ($playingPlayers as $key => $player) {
            $playingPlayers[$key]['win'] = false;
        }

        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'Surrender') {
                $surrenderedPlayerName = $order['PlayerName'];
                foreach ($playingPlayers as $key => $player) {
                    $playingPlayers[$key]['win'] = $player['name'] !== $surrenderedPlayerName;
                }

                $data['players'] = $playingPlayers;
                $game->data = $data;
                $game->save();

                return;
            }
        }

        // If no surrender, check who ended the replay last
        $lastEndReplay = null;
        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'EndReplay') {
                if (! $lastEndReplay || $order['TimeCode'] > $lastEndReplay['TimeCode']) {
                    $lastEndReplay = $order;
                }
            }
        }

        if ($lastEndReplay) {
            $winningPlayerName = $lastEndReplay['PlayerName'];
            foreach ($playingPlayers as $key => $player) {
                $playingPlayers[$key]['win'] = $player['name'] === $winningPlayerName;
            }

            $data['players'] = $playingPlayers;
            $game->data = $data;
            $game->save();
        }
    }
}
