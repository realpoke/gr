<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Models\Game;
use Illuminate\Support\Collection;

class UpdateGameImportantOrdersAction extends BaseAction
{
    public function __construct(private Game $game, private Collection $newData) {}

    public function execute(): self
    {
        $oldData = collect($this->game->data);

        $oldImportantOrders = collect($oldData->get('importantOrders', []));
        $newImportantOrders = collect($this->newData->get('importantOrders', []));

        $mergedOrders = $oldImportantOrders
            ->concat($newImportantOrders)
            ->unique(function ($order) {
                return $order['OrderCode'].'-'.$order['PlayerName'];
            })
            ->sortBy('TimeCode');

        $updatedData = $oldData->merge([
            'importantOrders' => $mergedOrders->values()->all(),
        ]);

        $this->game->data = $updatedData;

        return $this->game->save()
            ? $this->setSuccessful()
            : $this->setFailed('Failed to update game data.');
    }
}
