<?php

namespace App\Jobs;

use App\Actions\GenTool\DownloadGenToolReplayAction;
use App\Actions\GenTool\TextFileValidatorAction;
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
        private string $txtPath,
        private string $userId,
        private string $username,
        private int|string|null $uniqueId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $txtValidator = new TextFileValidatorAction($this->txtPath);
        $txtValidator->handle();

        if ($txtValidator->failed()) {
            $this->fail($txtValidator->getErrorMessage());

            return;
        }

        $downloader = new DownloadGenToolReplayAction($this->urlPath, $this->uniqueId);
        $downloader->handle();

        if ($downloader->failed()) {
            $this->fail($downloader->getErrorMessage());

            return;
        }

        ProcessReplayJob::dispatch($downloader->getFile(), $this->userId, $this->username);
    }
}
