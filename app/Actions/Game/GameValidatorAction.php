<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Enums\Game\GameStatusEnum;
use App\Enums\SideEnum;
use App\Models\Game;

class GameValidatorAction extends BaseAction
{
    private Game $validatedGame;

    public function getValidatedGame(): Game
    {
        return $this->validatedGame;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        if ($this->game->status != GameStatusEnum::PROCESSING) {
            return $this->setFailed('We only validate games that are processing');
        }

        $endReplayOrderValidator = $this->hasEnoughEndReplayOrders();
        if ($endReplayOrderValidator !== null) {
            return $this->setFailed($endReplayOrderValidator);
        }

        $gameTypeValidator = $this->validateGameType();
        if ($gameTypeValidator !== null) {
            return $this->setFailed($gameTypeValidator);
        }

        $botsValidator = $this->checkForBots();
        if ($botsValidator !== null) {
            return $this->setFailed($botsValidator);
        }

        $playerCountValidator = $this->validPlayerCount();
        if ($playerCountValidator !== null) {
            return $this->setFailed($playerCountValidator);
        }

        $uniquePlayers = $this->uniquePlayers();
        if ($uniquePlayers !== null) {
            return $this->setFailed($uniquePlayers);
        }

        $sidesValidator = $this->validPlayerSides();
        if ($sidesValidator !== null) {
            return $this->setFailed($sidesValidator);
        }

        return $this->setSuccessful();
    }

    private function validateGameType(): ?string
    {
        if (! $this->game->type->isValidGameType()) {
            return 'game type is not valid and is: '.$this->game->type->prettyName();
        }

        return null;
    }

    private function checkForBots(): ?string
    {
        if ($this->game->data['metaData']['hasBots']) {
            return 'Game has bots';
        }

        return null;
    }

    private function validPlayerCount(): ?string
    {
        $shouldBePlaying = $this->game->type->playersShouldBePlaying();

        if (
            $shouldBePlaying == null ||
            $this->game->users->count() !== $shouldBePlaying ||
            $this->game->data['metaData']['playersPlaying'] !== $shouldBePlaying
        ) {
            return 'Game has incorrect number of players '.$this->game->data['players']->count().' for game type: '.$this->game->type->prettyName();
        }

        return null;
    }

    private function hasEnoughEndReplayOrders(): ?string
    {
        $endReplayOrders = collect($this->game->data['importantOrders'])->filter(function ($order) {
            return $order['OrderName'] === 'EndReplay';
        });

        $hasEnough = $endReplayOrders->count() >= $this->game->users->count();

        return $hasEnough;
    }

    private function validPlayerSides(): ?string
    {
        foreach ($this->game->data['players'] as $player) {
            $side = SideEnum::tryFrom($player['side']);
            if ($side === null || ! $side->isValidSide()) {
                return 'player has invalid side set: '.$player['side'];
            }
        }

        return null;
    }

    private function uniquePlayers(): ?string
    {
        $playerNames = $this->game->users->pluck('pivot.player_name')->toArray();
        $isUnique = count($playerNames) === count(array_unique($playerNames));

        if (! $isUnique) {
            return 'Player names are not unique';
        }

        return null;
    }
}
