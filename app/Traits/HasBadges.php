<?php

namespace App\Traits;

use App\Enums\BadgeTypeEnum;
use App\Models\Badge;
use App\Models\Pivots\BadgeUserPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait HasBadges
{
    protected static function bootHasBadges()
    {
        static::deleting(function ($model) {
            $model->badges()->detach();
        });

        static::created(function (Model $model) {
            if ($model->fake) {
                return;
            }

            $joinedBadge = Badge::where('name', 'badge.name.joined')->first();
            if ($joinedBadge) {
                $model->giveBadge($joinedBadge);
            }

            $alphaBadge = Badge::where('name', 'badge.name.alpha-tester')->first(); // TODO: Remove when out of beta!
            if ($alphaBadge) {
                $model->giveBadge($alphaBadge);
            }
        });
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, BadgeUserPivot::TABLE)
            ->using(BadgeUserPivot::class)
            ->withPivot(BadgeUserPivot::FIELDS);
    }

    public function hasBadge(Badge $badge): bool
    {
        return $this->badges->contains('badge_id', $badge->id);
    }

    public function giveBadge(Badge $badge): bool
    {
        if ($this->hasBadge($badge)) {
            return $this->badges()
                ->where('badge_id', $badge->id)
                ->setTypeData($this->setupBadgeData($badge))
                ->save();
        }

        return (new BadgeUserPivot([
            'user_id' => $this->id,
            'badge_id' => $badge->id,
        ]))->setTypeData($this->setupBadgeData($badge), $badge->type)->save();
    }

    public function removeBadge(Badge $badge): bool
    {
        if ($this->hasBadge($badge)) {
            return (bool) $this->badges()->detach($badge->id);
        }

        return true;
    }

    public function badgePermissions(): Collection
    {
        return $this->badges()
            ->where('type', BadgeTypeEnum::PERMISSION)
            ->get()
            ->map(function ($badge) {
                return collect($badge->getTypeData() ?? [])
                    ->unique();
            })
            ->flatten();
    }

    public function hasBadgePermission($permission): bool
    {
        return $this->badgePermissions()->contains($permission);
    }

    private function setupBadgeData(Badge $badge): null|array|Carbon|int|bool
    {
        $foundBadge = $this->badges()->find($badge->id);
        if (is_null($foundBadge)) {
            return match ($badge->type) {
                BadgeTypeEnum::TIMESTAMP,
                BadgeTypeEnum::SINCE => now(),
                BadgeTypeEnum::ADDITIONAL => 1,
                BadgeTypeEnum::PERMISSION => [],
                BadgeTypeEnum::UNIQUE => true,
                default => null,
            };
        } else {
            return match ($badge->type) {
                BadgeTypeEnum::ADDITIONAL => $foundBadge->getTypeData() ? $foundBadge->getTypeData() + 1 : 1,
                default => null,
            };
        }
    }
}
