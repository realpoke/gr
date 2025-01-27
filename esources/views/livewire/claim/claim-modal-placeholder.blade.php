<div class="space-y-6 animate-pulse">
    <div>
        <flux:heading size="lg">{{ __('claim.placeholder.heading') }}</flux:heading>
        <flux:subheading>{{ __('claim.placeholder.subheading') }}</flux:subheading>
    </div>

    <flux:input disabled label="{{ __('claim.placeholder.input-label') }}" />

    <div class="flex">
        <flux:spacer />

        <flux:button disabled icon="loading" variant="primary">{{ __('claim.placeholder.action') }}</flux:button>
    </div>
</div>
