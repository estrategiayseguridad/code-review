<?php

use App\Livewire\Actions\Logout;
use Livewire\Attributes\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

}; ?>

<div>
    <nav x-data="{ open: false }" class="navbar navbar-expand-md bg-body shadow">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('dashboard') }}" wire:navigate>
                <x-application-logo :size="'xs'" />
            </a>

            <!-- Hamburger -->
            <button @click="open = ! open" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navigation Links -->
                <ul class="navbar-nav me-auto">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('projects.*')" wire:navigate>
                        <i class="fa-regular fa-folder-open pe-1"></i>
                        {{ __('Projects') }}
                    </x-nav-link>
                    <x-nav-link :href="route('languages.index')" :active="request()->routeIs('languages.*')" wire:navigate>
                        <i class="fa-solid fa-code pe-1"></i>
                        {{ __('Languages') }}
                    </x-nav-link>
                    <x-nav-link :href="route('config.general')" :active="request()->routeIs('config.*')" wire:navigate>
                        <i class="fa-solid fa-gear pe-1"></i>
                        {{ __('Configuration') }}
                    </x-nav-link>
                    @admin
                    <x-nav-link :href="route('history')" :active="request()->routeIs('history')" wire:navigate>
                        <i class="fa-solid fa-clock-rotate-left pe-1"></i>
                        {{ __('History') }}
                    </x-nav-link>
                    @endadmin
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas">
                            <i class="fa-solid fa-calendar-check"></i>
                        </button>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa-solid fa-user pe-2"></i>{{ Auth::user()->username }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('profile') }}" class="dropdown-item" wire:navigate>{{ __('Profile') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" wire:click="logout">{{ __('Logout') }}</a></li>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
