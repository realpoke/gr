<div class="space-y-6">
    <div class="flex gap-4 sm:flex-row flex-col sm:items-center items-start">
        <flux:input.group class="sm:max-w-80 w-full">
            <flux:input disabled icon="loading" placeholder="{{ __('label.loading') }}" />

            <flux:select disabled class="max-w-fit" variant="listbox">
                <flux:option selected>00</flux:option>
            </flux:select>
        </flux:input.group>

        <flux:input.group class="!w-fit">
            <flux:button disabled square icon="adjustments-horizontal" />
        </flux:input.group>

        <flux:switch disabled checked label="Live" />
    </div>

    <flux:separator text="{{ __('navigation.games') }}" />

    <div class="flex items-start justify-center p-2">
        <livewire:partials.awaiting-badge-component text="Live Games" />
    </div>

    <flux:card>
        <flux:table>
            <flux:columns>
                <flux:column>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse mr-4" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                    <flux:card class="flex-1 animate-pulse" style="padding: 0; border: 0;"><flux:heading class="opacity-0">-</flux:heading></flux:card>
                </flux:column>
            </flux:columns>

            <flux:rows>
                @for($i = 0; $i < 15; $i++)
                    <flux:row>
                        <flux:cell>
                            <flux:card class="w-full animate-pulse" style="padding: 0; border: 0;">
                                <flux:heading class="opacity-0">-</flux:heading>
                            </flux:card>
                        </flux:cell>
                    </flux:row>
                @endfor
            </flux:rows>
        </flux:table>
        <div class="flex justify-between mt-4 gap-1 flex-row">
            <flux:spacer />
            <flux:input disabled size="xs" class="max-w-12" />
        </div>
    </flux:card>
</div>

