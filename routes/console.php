<?php

use App\Jobs\CleanMapPlaysJob;
use App\Jobs\CleanStuffJob;
use App\Jobs\DownloadLatestsGenToolGamesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DownloadLatestsGenToolGamesJob)->evenInMaintenanceMode()->everyTenMinutes();
Schedule::job(new CleanStuffJob)->evenInMaintenanceMode()->dailyAt('05:00');

Schedule::job(new CleanMapPlaysJob(monthly: true))->evenInMaintenanceMode()->monthly();
Schedule::job(new CleanMapPlaysJob(weekly: true))->evenInMaintenanceMode()->weekly();
