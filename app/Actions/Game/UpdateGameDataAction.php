<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Actions\Replay\MoveBestReplayAction;
use App\Models\Game;
use Illuminate\Support\Collection;

class UpdateGameDataAction extends BaseAction
{
    public function __construct(
        private Game $game,
        private Collection $newData,
        private string $fileName,
    ) {}

    public function execute(): self
    {
        $oldData = collect($this->game->data);
        $oldImportantOrders = collect($oldData->get('importantOrders', []));
        $newImportantOrders = collect($this->newData->get('importantOrders', []));

        // Get the max TimeCode from the nested associative arrays
        $oldMaxTimeCode = $oldImportantOrders->map(fn ($order) => $order['TimeCode'] ?? null)->max();
        $newMaxTimeCode = $newImportantOrders->map(fn ($order) => $order['TimeCode'] ?? null)->max();

        if ($newMaxTimeCode > $oldMaxTimeCode) {

            if ($this->game->map?->isRanked() ?? false) {
                $mover = $this->moveReplay();
                if ($mover != null) {
                    return $this->setFailed('Failed to move replay: '.$mover);
                }
            }

            $mergedData = $oldData->merge($this->newData->except('importantOrders'));

            $this->game->data = $mergedData;

            return $this->game->save()
                ? $this->setSuccessful()
                : $this->setFailed('Failed to update game data.');
        } elseif (is_null($this->game->replay)) {
            $mover = $this->moveReplay();
            if ($mover != null) {
                return $this->setFailed('Failed to move replay: '.$mover);
            }
        }

        return $this->setSuccessful();
    }

    private function moveReplay(): ?string
    {
        $bestReplayMover = new MoveBestReplayAction(
            $this->fileName,
            $this->game->id
        );
        $bestReplayMover->handle();
        if ($bestReplayMover->failed()) {
            return 'Failed to move best replay: '.$bestReplayMover->getErrorMessage();
        }

        return null;
    }
}
