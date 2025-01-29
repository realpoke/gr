<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Enums\Game\GameTypeEnum;
use App\Events\PublicMapVerifiedEvent;
use App\Models\Map;

use function Illuminate\Support\defer;

class VerifyMapAction extends BaseAction
{
    public function __construct(private Map $map) {}

    public function execute(): self
    {
        if ($this->map->verified_at != null) {
            return $this->setSuccessful();
        }

        try {
            $csvFile = fopen(base_path('database/csv/GenRanksMapList.csv'), 'r');

            // Skip the first line (header)
            $headerLine = true;
            $i = 0;

            $mapNameToCheck = strtolower(str_replace(' ', '', $this->map->name));

            while (($data = fgetcsv($csvFile, 2000, ',')) !== false) {
                if (! $headerLine) {

                    $csvMapName = strtolower(str_replace(' ', '', $data[1]));

                    if ($mapNameToCheck != $csvMapName) {
                        continue;
                    }

                    // TODO: Make sure this map is not already verified by name

                    $types = explode(', ', $data[5]);
                    $enumTypes = [];

                    foreach ($types as $type) {
                        array_push($enumTypes, GameTypeEnum::tryFrom($type));
                    }
                    $enumTypes = array_unique($enumTypes, SORT_REGULAR);

                    $enumModes = [];
                    foreach ($enumTypes as $type) {
                        array_push($enumModes, $type->getGameMode());
                    }
                    $enumModes = array_unique($enumModes, SORT_REGULAR);

                    $this->map->update([
                        'name' => $data[1],
                        'verified_at' => now(),
                        'types' => $enumTypes,
                        'modes' => $enumModes,
                    ]);

                    defer(fn () => broadcast(new PublicMapVerifiedEvent($this->map->id)));

                    return $this->setSuccessful();
                } else {
                    $headerLine = false;
                }
                $i++;
            }

            fclose($csvFile);
        } catch (\Throwable $th) {
            return $this->setFailed('Failed to verify map: '.$th->getMessage());
        }

        return $this->setSuccessful();
    }
}
