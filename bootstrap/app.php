<?php

use App\Http\Middleware\EnsureInvalidSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: fn () => Route::middleware('web')->group(base_path('routes/auth.php'))
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('authenticate.page'));
        $middleware->redirectUsersTo(fn () => route('landing.page'));
        $middleware->web(EnsureInvalidSession::class);
        $middleware->trustProxies(at: '127.0.0.1', headers: Request::HEADER_X_FORWARDED_FOR);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
