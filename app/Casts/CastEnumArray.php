<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CastEnumArray implements CastsAttributes
{
    public function __construct(private string $enumClass) {}

    public function get($model, string $key, $value, array $attributes)
    {
        $array = json_decode($value ?? '[]', true) ?? [];

        return array_map(fn ($item) => $this->enumClass::from($item), $array);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode(array_map(fn ($item) => $item instanceof $this->enumClass ? $item->value : $item, $value));
    }
}
