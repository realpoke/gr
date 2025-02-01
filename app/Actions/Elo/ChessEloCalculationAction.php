<?php

namespace App\Actions\Elo;

use App\Actions\BaseAction;

class ChessEloCalculationAction extends BaseAction
{
    // TODO: K factor should be dynamic
    private const K_FACTOR = 32;

    private const ELO_DIVISOR = 400;

    private int $winnerGainedElo;

    private int $loserLostElo;

    public function __construct(
        private int $winnerElo,
        private int $loserElo
    ) {}

    public function execute(): self
    {
        $this->calculateEloChanges();

        return $this->setSuccessful();
    }

    private function calculateEloChanges(): void
    {
        $expectedWinnerScore = $this->calculateExpectedScore($this->winnerElo, $this->loserElo);
        $expectedLoserScore = $this->calculateExpectedScore($this->loserElo, $this->winnerElo);

        $newWinnerRating = $this->winnerElo + self::K_FACTOR * (1 - $expectedWinnerScore);
        $newLoserRating = $this->loserElo + self::K_FACTOR * (0 - $expectedLoserScore);

        $this->winnerGainedElo = round($newWinnerRating - $this->winnerElo);
        $this->loserLostElo = round($newLoserRating - $this->loserElo);
    }

    private function calculateExpectedScore(int $playerElo, int $opponentElo): float
    {
        return 1 / (1 + pow(10, ($opponentElo - $playerElo) / self::ELO_DIVISOR));
    }

    public function getWinnerElo(): int
    {
        return $this->winnerElo;
    }

    public function getLoserElo(): int
    {
        return $this->loserElo;
    }

    public function getWinnerGainedElo(): int
    {
        return $this->winnerGainedElo;
    }

    public function getLoserLostElo(): int
    {
        return $this->loserLostElo;
    }
}
