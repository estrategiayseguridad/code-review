<?php

use Livewire\Volt\Component;

new class extends Component
{

}; ?>

<div class="d-flex flex-wrap align-items-center">
    <ul class="nav me-auto">
        <x-nav-link :href="route('config.general')" :active="request()->routeIs('config.general')" wire:navigate>
                <i class="fa-solid fa-screwdriver-wrench pe-1"></i>
                {{ __('General') }}
        </x-nav-link>
        @admin
        <x-nav-link :href="route('config.users')" :active="request()->routeIs('config.users')" wire:navigate>
                <i class="fa-solid fa-users pe-1"></i>
                {{ __('Users') }}
        </x-nav-link>
        @endadmin
    </ul>
</div>
