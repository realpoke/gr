<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Models\Map;

class GetOrCreateMapAction extends BaseAction
{
    private Map $map;

    private bool $isNewMap = false;

    public function getMap(): Map
    {
        return $this->map;
    }

    public function isNewMap(): bool
    {
        return $this->isNewMap;
    }

    public function __construct(private string $mapHash, private string $mapFile) {}

    public function execute(): self
    {
        $oldMap = Map::where('hash', $this->mapHash)->first();
        if (! is_null($oldMap)) {
            $this->map = $oldMap;

            return $this->setSuccessful();
        }

        $newMap = Map::create([
            'hash' => $this->mapHash,
            'name' => collect(explode('/', $this->mapFile))->pop(),
        ]);
        if (! $newMap) {
            return $this->setFailed('Failed to create map.');
        }

        $this->isNewMap = true;
        $this->map = $newMap;

        return $this->setSuccessful();
    }
}
