<?php

namespace App\Livewire\Setting\Supporter;

use App\Enums\SupporterEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:Interface.{user.id},PrivateAccountEmailVerifiedEvent')]
class SupporterTab extends Component
{
    #[Computed()]
    public function paddleMonthly()
    {
        return $this->user->checkout(SupporterEnum::MONTHLY->priceId())->returnTo(route('setting.page', ['tab' => 'billing']));
    }

    #[Computed()]
    public function paddleAnnualy()
    {
        return $this->user->checkout(SupporterEnum::ANNUALY->priceId())->returnTo(route('setting.page', ['tab' => 'billing']));
    }

    #[Computed()]
    public function monthlyPrice(): string
    {
        return Number::currency(2.99, 'EUR');
    }

    #[Computed()]
    public function annualyPrice(): string
    {
        return Number::currency(29.90, 'EUR');
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
