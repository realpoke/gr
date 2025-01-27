@use(App\Enums\SupporterEnum)

<div>
    @if ($this->user->subscribed())
        <div class="flex flex-col md:flex-row justify-evenly gap-6">
            <flux:card class="lg:w-2/5 w-full space-y-6">
                <div>
                    <flux:heading>{{ __('setting.supporter.monthly-heading') }}</flux:heading>
                    <flux:subheading>{{ __('setting.supporter.monthly-subheading') }}</flux:subheading>
                </div>
                <flux:separator />
                <div class="flex items-baseline">
                    <flux:heading size="xl">{{ $this->monthlyPrice }}</flux:heading>
                    <flux:subheading size="sm">{{ __('setting.supporter.monthly-monthly') }}</flux:subheading>
                </div>

                @if (!$this->user->hasVerifiedEmail())
                    <div class="flex">
                        <flux:tooltip content="{{ __('tooltip.email-required') }}">
                            <div>
                                <flux:button variant="primary">{{ __('setting.supporter.monthly-action') }}</flux:button>
                            </div>
                        </flux:tooltip>
                    </div>
                @else
                    <livewire:partials.paddle-button-component
                        :disabled="!$this->user->hasVerifiedEmail()"
                        :items="collect($this->paddleMonthly->getItems())->toJson()"
                        :paddleId="$this->paddleMonthly->getCustomer()->paddle_id"
                        :customData="collect($this->paddleMonthly->getCustomData())->toJson()"
                        :returnUrl="$this->paddleMonthly->getReturnUrl()"
                        :text="__('setting.supporter.monthly-action')" />
                @endif
            </flux:card>

            <flux:card class="lg:w-2/5 w-full space-y-6">
                <div>
                    <flux:heading>{{ __('setting.supporter.annualy-heading') }}</flux:heading>
                    <flux:subheading>{{ __('setting.supporter.annualy-subheading') }}</flux:subheading>
                </div>
                <flux:separator />
                <div class="flex items-baseline">
                    <flux:heading size="xl">{{ $this->annualyPrice }}</flux:heading>
                    <flux:subheading size="sm">{{ __('setting.supporter.annualy-annualy') }}</flux:subheading>
                </div>

                @if (!$this->user->hasVerifiedEmail())
                    <div class="flex">
                        <flux:tooltip content="{{ __('tooltip.email-required') }}">
                            <div>
                                <flux:button variant="primary">{{ __('setting.supporter.annualy-action') }}</flux:button>
                            </div>
                        </flux:tooltip>
                    </div>
                @else

                    <livewire:partials.paddle-button-component
                        :disabled="!$this->user->hasVerifiedEmail()"
                        :items="collect($this->paddleAnnualy->getItems())->toJson()"
                        :paddleId="$this->paddleAnnualy->getCustomer()->paddle_id"
                        :customData="collect($this->PaddleAnnualy->getCustomData())->toJson()"
                        :returnUrl="$this->PaddleAnnualy->getReturnUrl()"
                        :text="__('setting.supporter.annualy-action')" />
                @endif
            </flux:card>
        </div>
    @else
        <flux:heading>YOU COOL THO</flux:heading>
    @endif
</div>
