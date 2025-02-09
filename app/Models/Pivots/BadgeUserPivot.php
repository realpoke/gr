<?php

namespace App\Models\Pivots;

use App\Enums\BadgeTypeEnum;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use RuntimeException;

class BadgeUserPivot extends Pivot
{
    public const TABLE = 'badge_user_pivot';

    protected $fillable = [
        'user_id',
        'badge_id',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public const FIELDS = [
        'data',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeData(BadgeTypeEnum $type): null|array|Carbon|int|bool
    {
        return match ($type) {
            BadgeTypeEnum::PERMISSION => $this->getPermissions(),
            BadgeTypeEnum::SINCE => $this->getSince(),
            BadgeTypeEnum::ADDITIONAL => $this->getAmount(),
            BadgeTypeEnum::TIMESTAMP => $this->getTimestamp(),
            BadgeTypeEnum::UNIQUE => true,
            default => throw new RuntimeException('Invalid badge type'),
        };
    }

    public function setTypeData($data, BadgeTypeEnum $type): ?self
    {
        if ($data === null) {
            return $this;
        }

        return match ($type) {
            BadgeTypeEnum::PERMISSION => $this->setPermissions($data),
            BadgeTypeEnum::SINCE => $this->setSince($data),
            BadgeTypeEnum::ADDITIONAL => $this->setAmount($data),
            BadgeTypeEnum::TIMESTAMP => $this->setTimestamp($data),
            BadgeTypeEnum::UNIQUE => $this,
            default => throw new RuntimeException('Invalid badge type'),
        };
    }

    public function getClasses(): string
    {
        return $this->color;
    }

    private function getSince(): ?Carbon
    {
        return Carbon::parse($this->data['since'] ?? null);
    }

    private function setSince(Carbon $since): self
    {
        $this->data = ['since' => $since->toDateTimeString()];

        return $this;
    }

    private function getAmount(): ?int
    {
        return (int) $this->data['amount'] ?? null;
    }

    private function setAmount(int $amount): self
    {
        $this->data = ['amount' => $amount];

        return $this;
    }

    private function getTimestamp(): ?Carbon
    {
        return Carbon::parse($this->data['timestamp'] ?? null);
    }

    private function setTimestamp(Carbon $timestamp): self
    {
        $this->data = ['timestamp' => $timestamp->toDateTimeString()];

        return $this;
    }

    private function getPermissions(): ?array
    {
        return (array) $this->data['permissions'] ?? null;
    }

    private function setPermissions(array $permissions): self
    {
        $this->data = ['permissions' => $permissions];

        return $this;
    }
}
