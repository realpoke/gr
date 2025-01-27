<?php

namespace App\Jobs;

use App\Actions\Clean\CleanOldClaimsAction;
use App\Actions\Clean\CleanOldReplaysAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CleanStuffJob implements ShouldQueue
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
        $cleanReplays = new CleanOldReplaysAction;
        $cleanReplays->handle();

        $cleanClaims = new CleanOldClaimsAction;
        $cleanClaims->handle();

        if ($cleanReplays->failed()) {
            Log::error('Failed to clean old replays: '.$cleanReplays->getErrorMessage());
        }

        if ($cleanClaims->failed()) {
            Log::error('Failed to clean old claims: '.$cleanClaims->getErrorMessage());
        }
    }
}
