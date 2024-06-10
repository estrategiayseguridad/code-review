<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />

        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="bg-body-tertiary">
        <div id="app">
            <livewire:layout.navigation />
            @hasSection('header')
            <div class="px-3 py-2 bg-dark-subtle border-bottom mb-3">
                <div class="container">
                    @yield('header')
                </div>
            </div>
            @endif
            <livewire:layout.offcanvas />
            <main class="container py-5">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
