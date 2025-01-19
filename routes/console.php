<?php

use App\Jobs\CleanStuffJob;
use App\Jobs\DownloadLatestsGenToolGamesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DownloadLatestsGenToolGamesJob)->everyTenMinutes();
Schedule::job(new CleanStuffJob)->dailyAt('05:00');
