<div class="flex flex-col mx-auto space-y-6">
    <div>
        <flux:heading size="xl">{{ __('setting.billing.title') }}</flux:heading>
        <flux:subheading>{{ __('setting.billing.subtitle') }}</flux:subheading>
    </div>
    <flux:button class="w-fit" wire:click="$parent.billingPortal" variant="primary">{{ __('setting.billing.action') }}</flux:button>
</div>
