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
        $winnerCount = 0;
        foreach ($this->game->data['players'] as $player) {
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
        $players = $data['players'];

        foreach ($players as $key => $player) {
            $players[$key]['win'] = false;
        }

        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'Surrender') {
                $surrenderedPlayerName = $order['PlayerName'];
                foreach ($players as $key => $player) {
                    $players[$key]['win'] = $player['name'] !== $surrenderedPlayerName;
                }

                $data['players'] = $players;
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
            foreach ($players as $key => $player) {
                $players[$key]['win'] = $player['name'] === $winningPlayerName;
            }

            $data['players'] = $players;
            $game->data = $data;
            $game->save();
        }
    }
}
