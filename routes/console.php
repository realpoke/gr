<?php

use App\Jobs\CleanStuffJob;
use App\Jobs\DownloadLatestsGenToolGamesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DownloadLatestsGenToolGamesJob)->evenInMaintenanceMode()->everyTenMinutes();
Schedule::job(new CleanStuffJob)->evenInMaintenanceMode()->dailyAt('05:00');
