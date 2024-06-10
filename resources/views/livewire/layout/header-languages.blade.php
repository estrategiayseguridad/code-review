<?php

use Livewire\Volt\Component;

new class extends Component
{

}; ?>

<div class="d-flex flex-wrap align-items-center">
    <ul class="nav me-auto">
        <x-nav-link :href="route('languages.index')" :active="request()->routeIs('languages.index')" wire:navigate>
                <i class="fa-solid fa-list pe-1"></i>
                {{ __('List') }}
        </x-nav-link>
        <x-nav-link :href="route('languages.extensions')" :active="request()->routeIs('languages.extensions')" wire:navigate>
                <i class="fa-regular fa-file-code pe-1"></i>
                {{ __('Extensions') }}
        </x-nav-link>
    </ul>
</div>
