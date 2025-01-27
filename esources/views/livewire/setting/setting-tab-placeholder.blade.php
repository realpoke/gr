<div class="animate-pulse opacity-25">
    <div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
        <div>
            <flux:heading>{{ __('setting.tab.placeholder.heading1') }}</flux:heading>
            <flux:subheading>{{ __('setting.tab.placeholder.subheading1') }}</flux:subheading>
        </div>

        <div class="space-y-6 md:col-span-2">
            <flux:input disabled label="{{ __('setting.tab.placeholder.field1') }}" />
            <flux:input disabled label="{{ __('setting.tab.placeholder.field2') }}" badge="{{ __('setting.tab.placeholder.badge1') }}" />
            <flux:input disabled label="{{ __('setting.tab.placeholder.field3') }}" />

            <div class="flex">
                <flux:button disabled><flux:icon.loading /> {{ __('label.loading') }}</flux:button>
            </div>
        </div>
    </div>

    <flux:separator />

    <div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
        <div>
            <flux:heading>{{ __('setting.tab.placeholder.heading2') }}</flux:heading>
            <flux:subheading>{{ __('setting.tab.placeholder.subheading2') }}</flux:subheading>
        </div>

        <div class="space-y-6 md:col-span-2">
            <flux:input disabled label="{{ __('setting.tab.placeholder.field4') }}" badge="{{ __('setting.tab.placeholder.badge2') }}" placeholder="{{ __('label.loading') }}" />

            <div class="flex">
                <flux:button disabled><flux:icon.loading />{{ __('setting.tab.placeholder.button') }}</flux:button>
            </div>
        </div>
    </div>
</div>
