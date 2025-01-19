<?php

namespace App\Jobs;

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
        private string $filePath,
        private string $userId,
        private string $username
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processed = new ProcessReplayAction($this->filePath);
        $processed->handle();

        if ($processed->failed()) {
            Storage::disk('replays')->delete($this->filePath);

            $this->job->fail(new \Exception($processed->getErrorMessage()));
        }

        $userAction = new GetOrCreateGenToolUserAction($this->userId, $this->username);
        $userAction->handle();

        if ($userAction->failed()) {
            $this->job->fail(new \Exception($userAction->getErrorMessage()));
        }

        // TODO: Also rename replay with prefix 'good_'
        // TODO: Check if best replay and save to database for game to download
        // TODO: Setup game action and attach user
    }
}
