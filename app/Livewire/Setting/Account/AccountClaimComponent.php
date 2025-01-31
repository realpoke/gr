<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Claim\RemoveClaimGentoolAction;
use App\Models\Gentool;
use App\Models\User;
use App\Traits\Rules\ClaimRules;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:Interface.{user.id},PrivateClaimingEvent')]
class AccountClaimComponent extends Component
{
    use ClaimRules;

    public function removeClaim(Gentool $gentool)
    {
        $remover = new RemoveClaimGentoolAction($gentool);
        $remover->handle();

        if ($remover->failed()) {
            Flux::toast(__('toast.claim-remove-failed'));
        }

        Flux::toast(__('toast.claim-removed'));
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed()]
    public function gentools(): Collection
    {
        return $this->user->gentools()->with(['plays' => function ($query) {
            $query->latest()->first();
        }])->get();
    }

    #[Computed()]
    public function maxClaims(): int
    {
        return self::computerClaimLimit();
    }
}
