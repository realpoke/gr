<div class="space-y-6">
    <flux:heading size="xl">{{ __('title.setting') }}</flux:heading>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="account" icon="cog-6-tooth">{{ __('navigation.account') }}</flux:tab>
            <flux:tab name="profile" icon="user">{{ __('navigation.profile') }}</flux:tab>
            <flux:tab name="friends" icon="user-group">{{ __('navigation.friends') }}</flux:tab>
            <flux:tab name="supporter" icon="gem">{{ __('navigation.supporter') }}</flux:tab>
            @if ($this->user->isCustomer() && $this->user->hasVerifiedEmail())
                <flux:tab name="billing" wire:click="billingPortal" icon="credit-card">{{ __('navigation.billing') }}</flux:tab>
            @endif
        </flux:tabs>

        <flux:tab.panel name="account">
            <livewire:setting.account.account-tab :lazy="$this->tab != 'account'" />
        </flux:tab.panel>

        <flux:tab.panel name="profile">
            <livewire:setting.profile.profile-tab :lazy="$this->tab != 'profile'" />
        </flux:tab.panel>

        <flux:tab.panel name="friends">
            <livewire:setting.friend.friend-tab :lazy="$this->tab != 'friends'" />
        </flux:tab.panel>

        <flux:tab.panel name="supporter">
            <livewire:setting.supporter.supporter-tab :lazy="$this->tab != 'supporter'" />
        </flux:tab.panel>

        <flux:tab.panel name="billing">
            <livewire:setting.billing.billing-tab :lazy="$this->tab != 'billing'" />
        </flux:tab.panel>
    </flux:tab.group>
</div>
