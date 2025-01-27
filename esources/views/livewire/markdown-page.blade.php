<div class="my-24 space-y-12">
    <div class="flex justify-center">
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png"
            name="{{ config('app.name') }}" class="dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
            name="{{ config('app.name') }}" class="hidden dark:flex" />
    </div>

    <flux:card class="flux mx-auto prose prose-zinc lg:prose-lg dark:prose-invert">
        {!! $this->markdown !!}
    </flux:card>
</div>
