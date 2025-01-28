<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Actions\Replay\ReplayParserAction;
use App\Enums\Game\GameStatusEnum;
use App\Models\Game;
use App\Models\Gentool;
use App\Models\User;

class SetupGameAction extends BaseAction
{
    private Game $game;

    private bool $allUploaded = false;

    private int $playingPlayersCount;

    private int $uploadedPlayingPlayersCount;

    public function getGame(): Game
    {
        return $this->game;
    }

    public function hasAllUploaded(): bool
    {
        return $this->allUploaded;
    }

    public function __construct(
        private ReplayParserAction $parser,
        private User $replayUserOwner,
        private string $fileName,
        private GenTool $gentool
    ) {}

    public function execute(): self
    {
        $gameGetter = new GetOrCreateGameAction($this->parser->all());
        $gameGetter->handle();
        if ($gameGetter->failed()) {
            return $this->setFailed('Failed to get game: '.$gameGetter->getErrorMessage());
        }

        $game = $gameGetter->getGame();
        $this->setAllPlayersUploaded($game);

        if ($this->playingPlayersCount === $this->uploadedPlayingPlayersCount) {
            return $this->setFailed('All players already uploaded');
        }

        if ($game->users()->where('user_id', $this->replayUserOwner->id)->exists()) {
            return $this->setFailed('A user is trying to upload a replay for a game they already uploaded: '.$game->id);
        }

        if ($game->status != GameStatusEnum::AWAITING) {
            $oldStatus = $game->status?->value;
            $game->status = GameStatusEnum::INVALID;
            $game->save();

            return $this->setFailed('Game is not awaiting: status is '.$oldStatus ?? 'null');
        }

        $gameDataUpdater = new UpdateGameDataAction($game, $this->parser->getGameData(), $this->fileName);
        $gameDataUpdater->handle();
        if ($gameDataUpdater->failed()) {
            return $this->setFailed('Failed to update game data: '.$gameDataUpdater->getErrorMessage());
        }

        $gameImportantOrdersUpdater = new UpdateGameImportantOrdersAction($game, $this->parser->getGameData());
        $gameImportantOrdersUpdater->handle();
        if ($gameImportantOrdersUpdater->failed()) {
            return $this->setFailed('Failed to update game importantOrders: '.$gameImportantOrdersUpdater->getErrorMessage());
        }

        $game->users()->attach($this->replayUserOwner, [
            'player_name' => $this->parser->getReplayOwnerName(),
            'gentool_id' => $this->gentool->id,
        ]);

        if ($this->playingPlayersCount === $this->uploadedPlayingPlayersCount + 1) {
            $game->status = GameStatusEnum::PROCESSING;
            $saved = $game->save();
            if (! $saved) {
                return $this->setFailed('Failed to save new game status');
            }
            $this->allUploaded = true;
        }

        $this->game = $game->refresh();

        return $this->setSuccessful();
    }

    private function setAllPlayersUploaded(Game $game): void
    {
        $playingPlayers = collect($game->data['players'])->filter(function ($player) {
            return $player['isPlaying'];
        });

        $uploadedPlayingPlayers = $game->users()
            ->whereIn('player_name', $playingPlayers->pluck('name'))
            ->count();

        $this->playingPlayersCount = $playingPlayers->count();
        $this->uploadedPlayingPlayersCount = $uploadedPlayingPlayers;
    }
}
