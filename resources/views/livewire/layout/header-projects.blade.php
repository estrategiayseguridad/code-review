<?php

use Livewire\Volt\Component;

new class extends Component
{

}; ?>

<div class="d-flex flex-wrap align-items-center">
    <ul class="nav me-auto">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <i class="fa-solid fa-list pe-1"></i>
                {{ __('List') }}
        </x-nav-link>
        <x-nav-link :href="route('projects.new')" :active="request()->routeIs('projects.new')" wire:navigate>
                <i class="fa-regular fa-plus pe-1"></i>
                {{ __('New') }}
        </x-nav-link>
    </ul>
</div>
