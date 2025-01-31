<?php

use App\Livewire\Game\IndexGamePage;
use App\Livewire\Game\ShowGamePage;
use App\Livewire\LandingPage;
use App\Livewire\Leaderboard\IndexLeaderboardPage;
use App\Livewire\Map\IndexMapPage;
use App\Livewire\MarkdownPage;
use App\Livewire\Profile\MeProfilePage;
use App\Livewire\Profile\ShowProfilePage;
use App\Livewire\Setting\SettingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing.page');

Route::get('/document/{document}', MarkdownPage::class)->name('document.page');

Route::get('/leaderboard', IndexLeaderboardPage::class)->name('index.leaderboard.page');

Route::get('/games', IndexGamePage::class)->name('index.game.page');
Route::get('/game/{hash}', ShowGamePage::class)->name('show.game.page');

Route::get('/maps', IndexMapPage::class)->name('index.map.page')->lazy();

Route::get('/profile/{user}', ShowProfilePage::class)->name('show.profile.page');

Route::middleware('auth')->group(function () {
    Route::get('/profile', MeProfilePage::class);

    Route::get('/settings', SettingPage::class)->name('setting.page');
});
