<?php

namespace App\Actions\Map;

use App\Actions\BaseAction;
use App\Events\PublicMapPlayedEvent;
use App\Models\Map;
use Illuminate\Support\Facades\Concurrency;

class AddMapPlayCountAction extends BaseAction
{
    public function __construct(private Map $map) {}

    public function execute(): self
    {
        $this->map->update([
            'plays' => $this->map->plays + 1,
            'plays_monthly' => $this->map->plays_monthly + 1,
            'plays_weekly' => $this->map->plays_weekly + 1,
        ]);

        Concurrency::defer(fn () => broadcast(new PublicMapPlayedEvent(
            $this->map->id,
            $this->map->plays,
            $this->map->plays_monthly,
            $this->map->plays_weekly
        )));

        return $this->setSuccessful();
    }
}
