<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;
use App\Traits\Rules\GameRules;
use Carbon\CarbonInterval;

class ReplayValidatorAction extends BaseAction
{
    use GameRules;

    public function __construct(private ReplayParserAction $parser) {}

    public function execute(): self
    {
        if ($this->parser->failed()) {
            return $this->setFailed('No validation, replay Parsing failed: '.$this->parser->getErrorMessage());
        }

        $gameBuildValidator = $this->validateGameBuild();
        if ($gameBuildValidator !== null) {
            return $this->setFailed($gameBuildValidator);
        }

        $longEnoughValidator = $this->longEnough();
        if ($longEnoughValidator !== null) {
            return $this->setFailed($longEnoughValidator);
        }

        $gameTypeValidator = $this->validateGameType();
        if ($gameTypeValidator !== null) {
            return $this->setFailed($gameTypeValidator);
        }

        $botsValidator = $this->hasAnyBots();
        if ($botsValidator !== null) {
            return $this->setFailed($botsValidator);
        }

        $playerCountValidator = $this->correctPlayerCount();
        if ($playerCountValidator !== null) {
            return $this->setFailed($playerCountValidator);
        }

        $sidesValidator = $this->validPlayerSides();
        if ($sidesValidator !== null) {
            return $this->setFailed($sidesValidator);
        }

        return $this->setSuccessful();
    }

    private function longEnough(): ?string
    {
        if ($this->parser->getMetaData()['gameInterval'] < $this->minimumGameInterval()->totalSeconds) {
            return 'Game interval is too short, must be minimum lenght to count. Interval is: '.CarbonInterval::seconds($this->parser->getMetaData()['gameInterval'] ?? 0)->format('%H:%I:%S');
        }

        return null;
    }

    private function correctPlayerCount(): ?string
    {
        $shouldBePlaying = $this->parser->getMetaData()['gameType']->playersShouldBePlaying();
        if ($shouldBePlaying !== null && $this->parser->getMetaData()['playersPlaying'] !== $shouldBePlaying) {
            return 'Replay has incorrect number of players '.$this->parser->getPlayers()->count().' for game type: '.$this->parser->getMetaData()['gameType']->prettyName();
        }

        return null;
    }

    private function validateGameType(): ?string
    {
        if ($this->parser->getMetaData()['gameType']->isValidGameType()) {
            return null;
        }

        return 'game type is not valid and is: '.$this->parser->getMetaData()['gameType']->prettyName();
    }

    private function validPlayerSides(): ?string
    {
        foreach ($this->parser->getGameData()['players'] as $player) {
            $side = $player['side'];
            if ($side === null || ! $side->isValidSide()) {
                return 'player has invalid side set: '.$player['side']->prettyName();
            }
        }

        return null;
    }

    private function hasAnyBots(): ?string
    {
        if ($this->parser->getMetaData()['hasBots']) {
            return 'replay has bots';
        }

        return null;
    }

    private function validateGameBuild(): ?string
    {
        $validBuilds = [
            [
                'BuildDate' => 'Mar 10 2005 13:47:03',
                'VersionMinor' => 4,
                'VersionMajor' => 1,
                'GameType' => 'GENREP',
            ],
            [
                'BuildDate' => 'Oct 17 2005 17:31:25',
                'VersionMinor' => 4,
                'VersionMajor' => 1,
                'GameType' => 'GENREP',
            ],
            [
                'BuildDate' => 'Sep  6 2012 11:51:43',
                'VersionMinor' => 4,
                'VersionMajor' => 1,
                'GameType' => 'GENREP',
            ],
        ];

        $gameBuild = $this->parser->getGameBuild();

        // Check if the game build matches any of the valid builds
        foreach ($validBuilds as $expectedBuild) {
            if ($this->compareGameBuild($gameBuild, $expectedBuild)) {
                return null;
            }
        }

        return 'game build did not match any valid builds, got build: '.$gameBuild;
    }

    private function compareGameBuild($gameBuild, $expectedBuild): bool
    {
        return $gameBuild['versionMajor'] == $expectedBuild['VersionMajor'] &&
               $gameBuild['versionMinor'] == $expectedBuild['VersionMinor'] &&
               $gameBuild['buildDate'] == $expectedBuild['BuildDate'] &&
               $gameBuild['gameType'] == $expectedBuild['GameType'];
    }
}
