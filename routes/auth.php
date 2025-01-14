<?php

use App\Livewire\Auth\Authenticate\AuthenticateUserPage;
use App\Livewire\Auth\Email\EmailVerificationNeededPage;
use App\Livewire\Auth\Email\HandleVerificationEmailPage;
use App\Livewire\Auth\Password\HandlePasswordResetLinkPage;
use App\Livewire\Auth\Password\PasswordVerificationNeededPage;
use App\Livewire\Auth\Password\SendPasswordResetLinkPage;
use App\Livewire\Auth\Register\RegisterUserPage;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', AuthenticateUserPage::class)->name('authenticate.page');
    Route::get('/register', RegisterUserPage::class)->name('register.page');

    Route::get('/password/reset', SendPasswordResetLinkPage::class)->name('resetpassword.page');

    Route::get('/password/reset/{token}', HandlePasswordResetLinkPage::class)
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', EmailVerificationNeededPage::class)
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', HandleVerificationEmailPage::class)
        ->middleware('signed')
        ->name('verification.verify');

    Route::get('/password/verify', PasswordVerificationNeededPage::class)
        ->name('password.confirm');
});
