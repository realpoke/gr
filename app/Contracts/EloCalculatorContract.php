<?php

namespace App\Contracts;

use App\Models\Game;

interface EloCalculatorContract extends BaseActionContract
{
    public function getGame(): Game;

    public function __construct(private Game $game);

    public function execute(): self;
}
