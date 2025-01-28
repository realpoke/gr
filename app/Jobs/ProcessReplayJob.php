<?php

namespace App\Jobs;

use App\Actions\Claim\AddCanClaimGameAction;
use App\Actions\Game\SetupGameAction;
use App\Actions\GenTool\GetOrCreateGenToolUserAction;
use App\Actions\Replay\ProcessReplayAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessReplayJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $fileName,
        private string $userId,
        private string $username
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processed = new ProcessReplayAction($this->fileName);
        $processed->handle();

        if ($processed->failed()) {
            Storage::disk('replays')->delete($this->fileName);

            $this->fail($processed->getErrorMessage());

            return;
        }

        $userAction = new GetOrCreateGenToolUserAction($this->userId, $this->username);
        $userAction->handle();

        if ($userAction->failed()) {
            $this->fail($userAction->getErrorMessage());

            return;
        }

        $setupGame = new SetupGameAction(
            $processed->getParsedReplay(),
            $userAction->getUser(),
            $this->fileName,
            $userAction->getGentool()
        );
        $setupGame->handle();

        if ($setupGame->failed()) {
            $this->fail($setupGame->getErrorMessage());

            return;
        }

        $claimGame = new AddCanClaimGameAction(
            $processed->getParsedReplay()->getReplayOwnerName(),
            $userAction->getGentool(),
            $setupGame->getGame()
        );
        $claimGame->handle();
        if ($claimGame->failed()) {
            $this->fail($claimGame->getErrorMessage());

            return;
        }

        if ($setupGame->hasAllUploaded()) {
            CalculateGameResultsJob::dispatch($setupGame->getGame()->id);
        }
    }
}
