<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;

class HashMapAction extends BaseAction
{
    private string $hash;

    public function getHash(): string
    {
        return $this->hash;
    }

    public function __construct(
        private string $mapFile,
        private string $mapCRC,
        private string $mapSize
    ) {}

    public function execute(): self
    {
        $this->hash = md5($this->mapFile.$this->mapCRC.$this->mapSize);

        return $this->setSuccessful();
    }
}
