<?php

namespace App\Traits;

use App\Models\Period;
use App\Models\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\JoinClause;

trait HasStats
{
    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }

    public static function bootHasStats(): void
    {
        static::deleting(function (Model $model) {
            $model->stats()->delete();
        });
    }

    public function scopeWithStats(Builder $query, Period $period): Builder
    {
        return $query->with(['stats' => function (Builder $q) use ($period) {
            return $q->where('period_id', $period->id);
        }])->whereNotNull('stats.rank');
    }

    public function scopeSortByStat(
        Builder $query,
        Period $period,
        string $column,
        string $direction = 'asc',
    ): Builder {
        return $query->join('stats', function (JoinClause $join) use ($period) {
            $join->on('stats.user_id', '=', 'users.id')
                ->where('stats.period_id', $period->id);
        })->orderBy('stats.'.$column, $direction)->select('users.*');
    }

    public function periods(): HasManyThrough
    {
        return $this->hasManyThrough(Period::class, Stat::class);
    }

    public function getOrCreateCurrentStatsForPeriod(Period $period): Stat
    {
        return $this->stats()->forPeriod($period)->first()
            ?? $this->stats()->create(['period_id' => $period->id]);
    }

    public function giveElo(int $elo, ?Stat $stat, ?Period $period): bool
    {
        return $this->changeElo(abs($elo), $stat, $period);
    }

    public function takeElo(int $elo, ?Stat $stat, ?Period $period): bool
    {
        return $this->changeElo(-abs($elo), $stat, $period);
    }

    public function giveEloToStat(int $elo, Stat $stat): bool
    {
        return $this->changeElo(abs($elo), stat: $stat);
    }

    public function takeEloFromStat(int $elo, Stat $stat): bool
    {
        return $this->changeElo(-abs($elo), stat: $stat);
    }

    public function removeStat(Stat $stat): bool
    {
        $statsToUpdate = $this->queryStatsToUpdateRank(0, $stat, $stat->period);

        $statsToUpdate->decrement('rank');

        return $stat->delete();
    }

    private function changeElo(
        int $eloChange,
        ?Stat $stat = null,
        ?Period $period = null,
    ): bool {
        if (is_null($stat) && is_null($period)) {
            throw new \RuntimeException('Must provide either stat or period');
        }

        if (is_null($stat)) {
            $stat = $this->getOrCreateCurrentStatsForPeriod($period);
        }

        /* $stat->lockForUpdate(); */

        $oldElo = $stat->elo ?? 1500;
        $newElo = max(1, $oldElo + $eloChange);

        $stat->elo = $newElo;

        if (! $stat->save()) {
            return false;
        }

        return $this->adjustStats($oldElo, $stat);
    }

    private function adjustStats(
        int $oldElo,
        Stat $stat,
    ): bool {
        if (is_null($stat->elo)) {
            return $this->setInitialStats($stat);
        }

        if ($oldElo < $stat->elo) {
            return $this->rankUpStats($oldElo, $stat);
        }

        if ($oldElo > $stat->elo) {
            return $this->rankDownStats($oldElo, $stat);
        }

        return true;
    }

    private function setInitialStats(
        Stat $stat,
    ): bool {
        $statsToUpdate = $this->queryStatsToUpdateRank(0, $stat);

        $bestStatRank = $statsToUpdate->count() > 0 ? $statsToUpdate->min('rank') : null;

        if (! is_null($bestStatRank)) {
            $stat->rank = $bestStatRank;
            $statsToUpdate->increment('rank');
        } else {
            $maxStatRank = Stat::where('period_id', $stat->period->id)
                ->where('user_id', '!=', $this->id)
                ->max('rank');

            $stat->rank = is_null($maxStatRank) ? 1 : ($maxStatRank + 1);
        }

        return $stat->save();
    }

    private function rankUpStats(
        int $oldElo,
        Stat $stat,
    ): bool {
        $statsToUpdate = $this->queryStatsToUpdateRank($oldElo, $stat);

        $statsToUpdateCount = $statsToUpdate->count();

        if (! is_null($statsToUpdateCount)) {
            $stat->decrement('rank', $statsToUpdateCount);
        }

        if (! $stat->save()) {
            return false;
        }

        $statsToUpdate->increment('rank');

        return true;
    }

    private function rankDownStats(
        int $oldElo,
        Stat $stat,
    ): bool {
        $statsToUpdate = $this->queryStatsToUpdateRank($oldElo, $stat);

        $statsToUpdateCount = $statsToUpdate->count();

        if (! is_null($statsToUpdateCount)) {
            $stat->increment('rank', $statsToUpdateCount);
        }

        if (! $stat->save()) {
            return false;
        }

        $statsToUpdate->decrement('rank');

        return true;
    }

    private function queryStatsToUpdateRank(
        int $oldElo,
        Stat $stat,
    ): Builder {
        $query = Stat::where('period_id', $stat->period->id)
            ->where('user_id', '!=', $this->id)
            ->whereBetween('elo', [min($oldElo, $stat->elo), max($oldElo, $stat->elo)]);

        if (! is_null($stat->rank)) {
            $query->where(function (Builder $q) use ($oldElo, $stat) {
                if ($stat->elo > $oldElo) {
                    $q->where('rank', '<=', $stat->rank);
                } else {
                    $q->where('rank', '>=', $stat->rank);
                }
            });
        }

        return $query;
        /* return $query->lockForUpdate(); */
    }
}
