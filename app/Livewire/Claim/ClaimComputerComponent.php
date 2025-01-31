<?php

namespace App\Livewire\Claim;

use App\Actions\Claim\StartClaimingComputerAction;
use App\Models\Game;
use App\Models\User;
use App\Traits\Rules\ClaimRules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:Interface.{user.id},PrivateClaimingEvent')]
class ClaimComputerComponent extends Component
{
    use ClaimRules;

    public ClaimComputerComponentForm $form;

    public Carbon $expiresAt;

    public string $claimName = '';

    public string $within = '';

    #[On('echo-private:Interface.{user.id},PrivateFoundClaimableComputerEvent')]
    public function boot()
    {
        $this->within = Carbon::now()->addMinutes(self::claimWithinMinutes() + 1)->diffForHumans();
        if ($this->isClaming()) {
            $this->expiresAt = $this->user->claim->expires_at;
            $this->claimName = $this->user->claim->name;
        } else {
            $this->expiresAt = now();
        }
    }

    public function startClaiming()
    {
        $startClaiming = new StartClaimingComputerAction($this->form);
        $startClaiming->handle();

        if ($startClaiming->failed()) {
            return;
        }

        $claim = $startClaiming->getClaim();
        $this->expiresAt = $claim->expires_at;
        $this->claimName = $claim->name;
    }

    public function placeholder()
    {
        return view('livewire.claim.claim-modal-placeholder');
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed()]
    public function isClaming(): bool
    {
        return $this->user->isClaming();
    }

    #[Computed()]
    public function gamesFound(): Collection
    {
        $ids = $this->user->claim->game_ids;
        if (empty($ids)) {
            return collect();
        }

        return Game::whereIn('id', $ids)->get();
    }

    #[Computed()]
    public function maxClaims(): int
    {
        return self::computerClaimLimit();
    }
}
