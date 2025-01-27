<flux:button
    variant="primary"
    :disabled="$disabled"
    class="paddle_button"
    data-items="{{ $items }}"
    data-customer-id="{{ $paddleId }}"
    data-custom-data="{{ $customData }}"
    data-success-url="{{ $returnUrl }}"
>
    {{ $text }}
</flux:button>
