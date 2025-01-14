<?php

use App\Livewire\LandingPage;
use App\Livewire\MarkdownPage;
use App\Livewire\Setting\SettingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing.page');

Route::get('/document/{document}', MarkdownPage::class)->name('document.page');

Route::middleware('auth')->group(function () {
    Route::get('/settings', SettingPage::class)->name('setting.page');
});
