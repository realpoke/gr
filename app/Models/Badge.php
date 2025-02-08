<?php

namespace App\Models;

use App\Enums\BadgeTypeEnum;
use App\Models\Pivots\BadgeUserPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use RuntimeException;

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

    public function getTypeData()
    {
        if ($this->pivot === null) {
            return null;
        }

        return match ($this->type) {
            BadgeTypeEnum::PERMISSION => $this->getPermissions(),
            BadgeTypeEnum::SINCE => $this->getSince(),
            BadgeTypeEnum::ADDITIONAL => $this->getAmount(),
            BadgeTypeEnum::TIMESTAMP => $this->getTimestamp(),
            default => throw new RuntimeException('Invalid badge type'),
        };
    }

    public function setTypeData($data)
    {
        if ($this->pivot === null) {
            return null;
        }

        return match ($this->type) {
            BadgeTypeEnum::PERMISSION => $this->setPermissions($data),
            BadgeTypeEnum::SINCE => $this->setSince($data),
            BadgeTypeEnum::ADDITIONAL => $this->setAmount($data),
            BadgeTypeEnum::TIMESTAMP => $this->setTimestamp($data),
            default => throw new RuntimeException('Invalid badge type'),
        };
    }

    public function getClasses(): string
    {
        return $this->color;
    }

    private function getSince(): ?Carbon
    {
        return Carbon::parse($this->pivot->data['since'] ?? null);
    }

    private function setSince(Carbon $since): self
    {
        $this->pivot->data['since'] = $since->toDateTimeString();

        return $this;
    }

    private function getAmount(): ?int
    {
        return (int) $this->pivot->data['amount'] ?? null;
    }

    private function setAmount(int $amount): self
    {
        $this->pivot->data['amount'] = $amount;

        return $this;
    }

    private function getTimestamp(): ?Carbon
    {
        return Carbon::parse($this->pivot->data['timestamp'] ?? null);
    }

    private function setTimestamp(Carbon $timestamp): self
    {
        $this->pivot->data['timestamp'] = $timestamp->toDateTimeString();

        return $this;
    }

    private function getPermissions(): ?array
    {
        return (array) $this->data['permissions'] ?? null;
    }

    private function setPermissions(array $permissions): self
    {
        $this->data['permissions'] = $permissions;

        return $this;
    }
}
