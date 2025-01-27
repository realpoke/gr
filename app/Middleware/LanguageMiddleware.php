<?php

namespace App\Middleware;

use App\Actions\Cookie\SetLocaleCookieAction;
use App\Enums\LanguageEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('preferred_language');

        if (is_null($locale)) {
            $browserLocale = $request->getPreferredLanguage(LanguageEnum::values());

            $locale = in_array($browserLocale, LanguageEnum::values()) ? $browserLocale : config('app.locale');

            app(SetLocaleCookieAction::class)($locale);
        } else {
            app(SetLocaleCookieAction::class)($locale);
        }

        return $next($request);
    }
}
