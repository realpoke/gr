<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    public function casts(): array
    {
        return [
            'data' => AsCollection::class,
        ];
    }

    public function replay(): HasOne
    {
        return $this->hasOne(Replay::class);
    }
}
