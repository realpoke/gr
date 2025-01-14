@extends('livewire.layouts.base')

@section('body')
    @sectionMissing('no-navigation')
        <div class="flex justify-center absolute lg:top-6 top-2 lg:left-6 left-2 space-x-6">
            <flux:button href="{{ url()->previous() }}" icon="arrow-uturn-left" variant="subtle" />
            <flux:brand href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png"
                name="{{ config('app.name') }}" class="dark:hidden" />
            <flux:brand href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
                name="{{ config('app.name') }}" class="hidden dark:flex" />
        </div>
    @endif
    <flux:main class="flex min-h-screen flex-col justify-center pt-12">
        <flux:heading size="xl" align="center">
            @yield('code')
            -
            @yield('title')
        </flux:heading>

        <flux:subheading align="center">
            @yield('message')
        </flux:subheading>
    </flux:main>
@endsection

