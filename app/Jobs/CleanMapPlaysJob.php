<?php

namespace App\Jobs;

use App\Actions\Clean\CleanMapPlaysAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanMapPlaysJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private bool $monthly = false, private bool $weekly = false)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $action = new CleanMapPlaysAction($this->monthly, $this->weekly);
        $action->handle();

        if ($action->failed()) {
            $this->job->fail(new \Exception($action->getErrorMessage()));
        }
    }
}
