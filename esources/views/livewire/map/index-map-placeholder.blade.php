<div class="space-y-6">
    <div class="flex gap-4 flex-row">
        <flux:input disabled class="sm:max-w-80" icon="loading" placeholder="{{ __('label.loading') }}" />
        <flux:input.group class="w-auto">
            <flux:button disabled square icon="adjustments-horizontal" />
        </flux:input.group>
    </div>

    <flux:separator text="{{ __('navigation.maps') }}" />

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
        <div class="flex justify-between mt-4 gap-1 flex-col md:flex-row">
            <flux:input disabled size="xs" class="md:max-w-44 w-full" />
            <flux:input disabled size="xs" class="md:max-w-64 w-full" />
        </div>
    </flux:card>
</div>

