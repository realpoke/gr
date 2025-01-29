<?php

namespace App\Actions\Claim;

use App\Actions\BaseAction;
use App\Events\PrivateClaimingEvent;
use App\Livewire\Claim\ClaimComputerComponentForm;
use App\Models\Claim;
use App\Traits\Rules\ClaimRules;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Str;

class StartClaimingComputerAction extends BaseAction
{
    use ClaimRules, WithLimits;

    private string $namePrefix = 'gr!';

    private Claim $claim;

    public function __construct(private ClaimComputerComponentForm $form) {}

    public function getClaim(): Claim
    {
        return $this->claim;
    }

    public function execute(): self
    {
        $this->limitAction('claim-computer', forField: 'form.password');

        if (Auth::user()->isClaming()) {
            $this->form->addError('password', __('auth.already-claiming'));

            return $this->setFailed('You are already claiming a computer. User: '.Auth::user()->id);
        }

        $this->form->validate();

        for ($i = 0; $i < 20; $i++) {
            $randomName = $this->namePrefix.Str::random(4);

            $check = Claim::where('name', $randomName)
                ->first();

            if ($check === null) {
                break;
            }
        }

        $this->claim = Claim::updateOrCreate([
            'user_id' => Auth::user()->id,
        ], [
            'name' => $randomName,
            'expires_at' => now()->addMinutes(self::claimWithinMinutes()),
            'private' => $this->form->private,
            'game_ids' => null,
        ]);

        $this->form->reset();

        Concurrency::defer(fn () => broadcast(new PrivateClaimingEvent));

        return $this->setSuccessful();
    }
}
