@extends('livewire.layouts.base')

@section('body')
    <flux:main class="flex min-h-screen flex-col justify-center py-12">
        <div class="absolute top-0 right-0 p-4 space-x-2 text-gray-400 sm:p-6">
            <livewire:partials.language-select-component />
            <livewire:partials.dark-mode-picker-component />
        </div>
        @yield('content')

        @isset($slot)
            {{ $slot }}
        @endisset
    </flux:main>
@endsection

