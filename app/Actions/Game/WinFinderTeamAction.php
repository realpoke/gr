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
        $this->findAndSetWinner($this->game);

        $this->game = $this->game->refresh();

        return $this->setSuccessful();
    }

    private function findAndSetWinner(Game $game): void
    {
        $data = $game->data;
        $importantOrders = $data['importantOrders'];
        $players = $data['players'];

        // Track surrendered players
        $surrenderedPlayers = [];
        $teamSurrenders = [];

        // Check for team surrenders first
        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'Surrender') {
                $surrenderedPlayerName = $order['PlayerName'];
                $surrenderedPlayers[] = $surrenderedPlayerName;
                $surrenderedTeam = $this->getPlayerTeam($players, $surrenderedPlayerName);
                $teamSurrenders[$surrenderedTeam][] = $surrenderedPlayerName;
            }
        }

        // Get team members from players
        $teams = [];
        foreach ($players as $player) {
            $teams[$player['team']][] = $player['name'];
        }

        // Check if all teams surrendered
        $allTeamsSurrendered = true;
        foreach ($teams as $teamId => $teamMembers) {
            if (! isset($teamSurrenders[$teamId]) || count($teamSurrenders[$teamId]) !== count($teamMembers)) {
                $allTeamsSurrendered = false;
                break;
            }
        }

        // If all teams surrendered, determine winner by last to surrender
        if ($allTeamsSurrendered) {
            $lastSurrender = null;
            foreach ($importantOrders as $order) {
                if ($order['OrderName'] === 'Surrender') {
                    if (! $lastSurrender || $order['TimeCode'] > $lastSurrender['TimeCode']) {
                        $lastSurrender = $order;
                    }
                }
            }

            // The team that surrendered last wins
            if ($lastSurrender) {
                $winningTeam = $this->getPlayerTeam($players, $lastSurrender['PlayerName']);
                foreach ($players as $key => $player) {
                    $players[$key]['win'] = ($player['team'] === $winningTeam);
                }
                $data['players'] = $players;
                $game->data = $data;
                $game->save();

                return;
            }
        }

        // Check if any team has all members surrendered
        foreach ($teams as $teamId => $teamMembers) {
            if (isset($teamSurrenders[$teamId]) && count($teamSurrenders[$teamId]) === count($teamMembers)) {
                // This team has fully surrendered - other teams win
                foreach ($players as $key => $player) {
                    $players[$key]['win'] = $player['team'] !== $teamId;
                }
                $data['players'] = $players;
                $game->data = $data;
                $game->save();

                return;
            }
        }

        // If no complete team surrender, check who ended the replay last
        // but ignore EndReplay from surrendered players
        $lastEndReplay = null;
        foreach ($importantOrders as $order) {
            if ($order['OrderName'] === 'EndReplay' && ! in_array($order['PlayerName'], $surrenderedPlayers)) {
                if (! $lastEndReplay || $order['TimeCode'] > $lastEndReplay['TimeCode']) {
                    $lastEndReplay = $order;
                }
            }
        }

        if ($lastEndReplay) {
            $winningPlayerName = $lastEndReplay['PlayerName'];
            $winningTeam = $this->getPlayerTeam($players, $winningPlayerName);

            foreach ($players as $key => $player) {
                $players[$key]['win'] = $player['team'] === $winningTeam;
            }
            $data['players'] = $players;
            $game->data = $data;
            $game->save();
        }
    }

    private function getPlayerTeam(array $players, string $playerName): string
    {
        foreach ($players as $player) {
            if ($player['name'] === $playerName) {
                return $player['team'];
            }
        }

        return '-1';
    }
}
