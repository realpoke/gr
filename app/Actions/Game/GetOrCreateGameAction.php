<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Actions\Map\AddMapPlayCountAction;
use App\Actions\Map\GetOrCreateMapAction;
use App\Actions\Map\VerifyMapAction;
use App\Models\Game;
use Illuminate\Support\Collection;

class GetOrCreateGameAction extends BaseAction
{
    private Game $game;

    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Collection $allParserData) {}

    public function execute(): self
    {
        $foundGame = Game::where('hash', $this->allParserData['gameHash'])->first();

        if ($foundGame) {
            $this->game = $foundGame;

            return $this->setSuccessful();
        }

        $mapper = new GetOrCreateMapAction($this->allParserData['metaData']['MapHash'], $this->allParserData['metaData']['MapFile']);
        $mapper->handle();
        if ($mapper->failed()) {
            return $this->setFailed('Failed to get or create map: '.$mapper->getErrorMessage());
        }

        if ($mapper->isNewMap()) {
            $mapVerifier = new VerifyMapAction($mapper->getMap());
            $mapVerifier->handle();
            if ($mapVerifier->failed()) {
                return $this->setFailed('Failed to verify map: '.$mapVerifier->getErrorMessage());
            }
        }

        $game = new Game([
            'hash' => $this->allParserData['gameHash'],
            'data' => $this->allParserData['gameData'],
            'map_id' => $mapper->getMap()->id,
        ]);

        $game->type = $this->allParserData['metaData']['gameType'];
        $saved = $game->save();
        if (! $saved) {
            return $this->setFailed('Failed to save game to database.');
        }

        $this->game = $game->refresh();

        $mapper = new AddMapPlayCountAction($this->game->map);
        $mapper->handle();
        if ($mapper->failed()) {
            return $this->setFailed('Failed to add map play count: '.$mapper->getErrorMessage());
        }

        return $this->setSuccessful();
    }
}
