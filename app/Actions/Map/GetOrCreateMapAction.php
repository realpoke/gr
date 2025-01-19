<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Models\Map;

class GetOrCreateMapAction extends BaseAction
{
    protected Map $map;

    public function getMap(): Map
    {
        return $this->map;
    }

    public function __construct(private string $mapHash, private string $mapFile) {}

    public function execute(): self
    {
        $this->map = Map::where('hash', $this->mapHash)->first();
        if ($this->map) {
            return $this->setSuccessful();
        }

        $newMap = Map::create([
            'hash' => $this->mapHash,
            'name' => collect(explode('/', $this->mapFile))->pop(),
        ]);
        if (! $newMap) {
            return $this->setFailed('Failed to create map.');
        }

        $this->map = $newMap;

        return $this->setSuccessful();
    }
}
