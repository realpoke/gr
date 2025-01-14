<?php

namespace App\Traits;

use App\Exceptions\TooManyRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait WithLimits
{
    public function limitAction(
        string $to,
        int $perMinute = 5,
        int $decaySeconds = 120,
        string $forField = 'slow',
        bool $throw = true,
        ?string $prefix = null,
    ): ?string {
        try {
            $this->rateLimitAction($prefix, $perMinute, $decaySeconds);
        } catch (TooManyRequestsException $exception) {
            $message = __('exception.slow-down', ['seconds' => $exception->secondsUntilAvailable, 'to' => __('limit.'.$to)]);
            if ($throw) {
                throw ValidationException::withMessages([
                    $forField => $message,
                ]);
            }

            return $message;
        }

        return null;
    }

    public function clearLimitAction(?string $prefix = null): void
    {
        $key = $this->getRateLimitKeyAction($prefix);

        RateLimiter::clear($key);
    }

    private function getRateLimitKeyAction(?string $prefix = 'main'): string
    {
        $action ??= debug_backtrace(limit: 1)[0]['class'];

        return sha1($prefix.'|'.$action.'|'.request()->ip());
    }

    private function hitRateLimiterAction(?string $prefix, int $decaySeconds): void
    {
        $key = $this->getRateLimitKeyAction($prefix);

        RateLimiter::hit($key, $decaySeconds);
    }

    private function rateLimitAction(?string $prefix, int $maxAttempts, int $decaySeconds): void
    {
        $key = $this->getRateLimitKeyAction($prefix);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new TooManyRequestsException($key, RateLimiter::availableIn($key));
        }

        $this->hitRateLimiterAction($prefix, $decaySeconds);
    }
}
