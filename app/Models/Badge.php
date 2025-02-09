<?php

namespace App\Models;

use App\Enums\BadgeTypeEnum;
use App\Models\Pivots\BadgeUserPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'color',
        'type',
        'icon',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'type' => BadgeTypeEnum::class,
            'data' => 'array',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->hasMany(Badge::class)
            ->using(BadgeUserPivot::class)
            ->withPivot(BadgeUserPivot::FIELDS);
    }

    public function getTypeData(): null|array|Carbon|int|bool
    {
        if ($this->pivot === null) {
            return null;
        }

        return $this->pivot->getTypeData($this->type);
    }

    public function setTypeData($data): ?self
    {
        if ($this->pivot === null) {
            return null;
        } elseif ($data === null) {
            return $this;
        }

        return $this->pivot->setTypeData($data, $this->type);
    }
}
