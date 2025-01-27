@use(App\Enums\LanguageEnum)

<flux:dropdown position="bottom">
    <flux:button variant="subtle" square>
        <flux:icon.language />
    </flux:button>

    <flux:menu>
        <flux:menu.radio.group wire:model.live="language">
            @foreach (LanguageEnum::cases() as $language)
                <flux:menu.radio class="cursor-pointer" value="{{ $language->value }}"><flux:icon inset="top bottom" icon="flag-{{ $language->value }}" class="mr-2" />{{ $language->getName() }}</flux:menu.radio>
            @endforeach
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
