<?php

namespace App\Models;

use App\Models\Pivots\GameUserPivot;
use App\Traits\HasBadges;
use App\Traits\HasStats;
use App\Traits\Rules\ClaimRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, ClaimRules, HasBadges, HasFactory, HasStats, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'fake',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'email',
        'email_verified_at',
        'fake',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fake' => 'bool',
        ];
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, GameUserPivot::TABLE)
            ->using(GameUserPivot::class)
            ->withPivot(GameUserPivot::FIELDS)
            ->withTimestamps();
    }

    public function gentools(): HasMany
    {
        return $this->hasMany(Gentool::class);
    }

    public function page(): string
    {
        return route('show.profile.page', $this->id);
    }

    public function claim(): HasOne
    {
        return $this->hasOne(Claim::class);
    }

    public function isClaming(): bool
    {
        return ! ($this->claim?->isExpired() ?? true);
    }

    public function isCustomer(): bool
    {
        return $this->customer()->exists();
    }

    public function canClaimMoreComputers(): bool
    {
        return $this->isClaming() || $this->gentools->count() < self::computerClaimLimit();
    }

    public function claimCount(): int
    {
        return $this->gentools()->count();
    }
}
