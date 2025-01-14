<?php

namespace App\Traits\Rules;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait AuthRules
{
    final public static function useEmail(): array
    {
        return ['required', 'email'];
    }

    final public static function usePassword(): array
    {
        return ['required', 'min:8'];
    }

    final public static function useCurrentPassword(): array
    {
        return ['required', 'string', 'current_password'];
    }

    final public static function useTwoFactorCode(): array
    {
        return ['required', 'numeric', 'digits:6'];
    }

    final public static function useRemember(): array
    {
        return ['boolean'];
    }

    final public static function useRequiredString(): array
    {
        return ['required', 'string'];
    }

    final public static function useLogoutOther(): array
    {
        return ['boolean'];
    }

    final public static function setUsername(): array
    {
        return ['required', 'string', 'max:28', 'min:2'];
    }

    final public static function setEmail(?Model $model = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            ($model ?
                Rule::unique(User::class, 'email')->ignoreModel($model) :
                Rule::unique(User::class, 'email')),
        ];
    }

    final public static function setPassword(string $confirmField = 'passwordConfirmation'): array
    {
        return ['required', 'min:8', 'same:'.$confirmField];
    }

    final public static function setTerms(): array
    {
        return ['accepted'];
    }
}
