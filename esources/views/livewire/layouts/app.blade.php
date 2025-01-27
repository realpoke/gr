@extends('livewire.layouts.base')

@section('body')
    <livewire:partials.navigation-component />

    <flux:main container>
        @yield('content')

        @isset($slot)
            {{ $slot }}
        @endisset
    </flux:main>
@endsection

