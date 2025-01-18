<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.account.logout-other-devices.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.account.logout-other-devices.subheading') }}</flux:subheading>
    </div>

    <div class="space-y-6 md:col-span-2">
        <form wire:submit="logoutOtherDevices" class="space-y-6">
            <flux:input wire:model="form.password" type="password" label="{{ __('label.current-password') }}" />
            <div class="flex gap-2">
                <flux:button type="submit">{{ __('setting.account.logout-other-devices.action') }}</flux:button>
                <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
            </div>
        </form>

        <div>
            <flux:table>
                <flux:columns>
                    <flux:column>{{ __('label.device') }}</flux:column>
                    <flux:column>{{ __('label.ip-address') }}</flux:column>
                    <flux:column class="md:block hidden">{{ __('label.last-active') }}</flux:column>
                </flux:columns>

                <flux:rows>
                    @foreach($this->sessions as $session)
                        <flux:row>
                            <flux:cell class="flex items-center gap-2">
                                @if ($session->agent->isDesktop())
                                    <flux:icon.computer-desktop />
                                @elseif ($session->agent->isMobile())
                                    <flux:icon.device-phone-mobile />
                                @else
                                    <flux:icon.question-mark-circle />
                                @endif
                                <flux:text class="flex flex-col lg:flex-row">
                                    @if ($session->is_current_device)
                                        <span class="lg:hidden block">
                                            <flux:badge inset="top right" size="sm">{{ __('label.current') }}</flux:badge>
                                        </span>
                                    @endif
                                    <span>
                                        {{ $session->agent->platform() ?? __('label.unknown') }}
                                    </span>
                                    <span class="lg:block hidden mx-1">-</span>
                                    <span>
                                        {{ $session->agent->browser() ?? __('label.unknown') }}
                                    </span>
                                </flux:text>
                                @if ($session->is_current_device)
                                    <flux:badge class="lg:inline-flex hidden" inset="top bottom" size="sm">{{ __('label.current') }}</flux:badge>
                                @endif
                            </flux:cell>
                            <flux:cell>
                                <span>
                                    {{ $session->ip_address }}
                                </span>
                                <span class="md:hidden block">
                                    {{ $session->last_active }}
                                </span>
                            </flux:cell>
                            <flux:cell class="md:table-cell hidden">{{ $session->last_active }}</flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </div>
    </div>
</div>
