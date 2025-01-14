<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ isset($title) ? __($title) : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')
    @fluxStyles
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    @yield('body')

    @persist('toast')
        <flux:toast />
    @endpersist

    @auth
        <livewire:partials.user-interface-handler-component />
    @endauth

    @fluxScripts
</body>

</html>

