<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.account.claim.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.account.claim.subheading') }}</flux:subheading>
    </div>

    <div class="md:col-span-2">
        @if ($this->gentools->count() > 0)
            <div class="space-y-6 mb-6">
                <div>
                    <flux:heading>{{ __('setting.account.claim.has-claims.heading') }}</flux:heading>
                    <flux:subheading>{{ __('setting.account.claim.has-claims.subheading', ['max' => 2, 'current' => 1]) }}</flux:subheading>
                </div>

                <flux:table>
                    <flux:columns>
                        <flux:column>{{ __('label.id') }}</flux:column>
                        <flux:column>{{ __('label.claimed-at') }}</flux:column>
                        <flux:column>{{ __('label.last-active') }}</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($this->gentools as $gentool)
                            <flux:row :key="$gentool->id">
                                <flux:cell>{{ $gentool->gentool_id }}</flux:cell>
                                <flux:cell>{{ $gentool->updated_at }}</flux:cell>
                                <flux:cell>{{ $gentool->plays->first()->created_at->diffForHumans() }}</flux:cell>
                                <flux:cell>
                                    <flux:dropdown>
                                        <flux:button variant="ghost" squre icon="ellipsis-horizontal" size="sm" inset="top bottom" />

                                        <flux:menu>
                                            <flux:menu.item wire:navigate href="{{ $gentool->plays->first()->game->page() }}" icon="swords">{{ __('setting.account.claim.activity-details') }}</flux:menu.item>

                                            <flux:menu.separator />

                                            <flux:menu.item wire:click="removeClaim('{{ $gentool->id }}')" icon="trash" variant="danger">{{ __('setting.account.claim.revoke') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </div>
        @else
            <div class="mb-6">
                <flux:heading>{{ __('setting.account.claim.no-claims.heading') }}</flux:heading>
                <flux:subheading>{{ __('setting.account.claim.no-claims.subheading') }}</flux:subheading>
            </div>
        @endif
        @if ($this->user->canClaimMoreComputers())
            <livewire:claim.claim-computer-component />
        @endif
    </div>
</div>
