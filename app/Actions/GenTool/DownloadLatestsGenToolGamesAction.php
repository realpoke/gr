<?php

namespace App\Actions\GenTool;

use App\Actions\BaseAction;
use App\Jobs\DownloadGenToolGameJob;

class DownloadLatestsGenToolGamesAction extends BaseAction
{
    public function execute(): self
    {
        $delayTime = now();

        $gameInfo = new GetLatestsGenToolGamesAction;
        $gameInfo = $gameInfo->handle();

        if ($gameInfo->failed()) {
            return $this->setFailed('Failed to download latest GenTool replays: '.$gameInfo->getErrorMessage());
        }

        // Spread out the downloads over the next 10 minutes
        $replays = $gameInfo->getReplays();
        $textPaths = $gameInfo->getTextPaths();
        $replayCount = $replays->count();

        $totalDelayMs = 10 * 60 * 1000; // Total delay 10 minutes in milliseconds
        $baseDelayMs = (int) round($totalDelayMs / $replayCount);

        foreach ($gameInfo->getReplayPaths() as $index => $urlPath) {
            $replay = $replays->get($index);
            $txt = $textPaths->get($index);

            DownloadGenToolGameJob::dispatch($urlPath, $txt, $replay['userid'], $replay['username'], $index)->delay($delayTime);

            $delayTime->addMilliseconds($baseDelayMs);
        }

        return $this->setSuccessful();
    }
}
