<flux:modal name="claim-computer-modal" class="md:w-96 min-h-48"
    x-data="{ clear: false }"
    x-on:close="clear = !clear"
>
    <livewire:claim.claim-computer-component lazy/>
</flux:modal>
