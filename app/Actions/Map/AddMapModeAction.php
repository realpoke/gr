<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Enums\Game\GameModeEnum;
use App\Models\Map;

class AddMapModeAction extends BaseAction
{
    public function __construct(private Map $map, private GameModeEnum $mode) {}

    public function execute(): self
    {
        $this->map->update([
            'modes' => array_merge($this->map->modes, [$this->mode]),
        ]);

        return $this->setSuccessful();
    }
}
