<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Models\Map;

class AddMapPlayCountAction extends BaseAction
{
    public function __construct(private Map $map) {}

    public function execute(): self
    {
        $this->map->increment('plays');

        return $this->setSuccessful();
    }
}
