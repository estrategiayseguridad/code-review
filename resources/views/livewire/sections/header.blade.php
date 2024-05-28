<?php

use Livewire\Volt\Component;

new class extends Component
{

}; ?>

<nav class="navbar navbar-expand-md shadow-sm">
    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('images/logo-xs.png') }}" class="me-2" alt="logo" width="25" height="25">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                {{-- <x-nav-link :href="route('home')" :active="request()->routeIs('home')" wire:navigate>
                    {{ __('Dashboard') }}
                </x-nav-link> --}}
                <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')" wire:navigate>
                    <i class="fa-regular fa-folder-open pe-2"></i>{{ __('Projects') }}
                </x-nav-link>
                <x-nav-link :href="route('languages.index')" :active="request()->routeIs('languages.index')" wire:navigate>
                    <i class="fa-solid fa-code pe-2"></i>{{ __('Languages') }}
                </x-nav-link>
                <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')" wire:navigate>
                    <i class="fa-regular fa-address-book pe-2"></i>{{ __('Roles') }}
                </x-nav-link>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">

                <!-- Authentication Links -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <i class="fa-regular fa-user pe-1"></i>
                        {{ Auth::user()->username }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
