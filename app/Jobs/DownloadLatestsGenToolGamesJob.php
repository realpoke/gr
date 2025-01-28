<?php

namespace App\Jobs;

use App\Actions\GenTool\DownloadLatestsGenToolGamesAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DownloadLatestsGenToolGamesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $job = new DownloadLatestsGenToolGamesAction;
        $job->handle();

        if ($job->failed()) {
            $this->fail($job->getErrorMessage());

            return;
        }
    }
}
