<?php

namespace App\Jobs;

use App\Actions\GenTool\DownloadGenToolReplayAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DownloadGenToolGameJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $urlPath,
        private string $userId,
        private string $username,
        private int|string|null $uniqueId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $downloader = new DownloadGenToolReplayAction($this->urlPath, $this->uniqueId);
        $downloader->handle();

        if ($downloader->failed()) {
            $this->job->fail(new \Exception($downloader->getErrorMessage()));
        }

        ProcessReplayJob::dispatch($downloader->getFile(), $this->userId, $this->username);
    }
}
