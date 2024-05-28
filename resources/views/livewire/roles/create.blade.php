<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Spatie\Permission\Models\Role;

new class extends Component
{
    #[Rule('required|string|unique:roles,name', as: 'Nombre')]
    public $name = '';

    public function save(): void
    {
        Role::create($this->validate());
        $this->redirectRoute('roles.index');
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-5">
            <div class="card">
                <div class="card-header">{{ __('Role') }}</div>
                <div class="card-body">
                    <form wire:submit="save" class="d-grid gap-3">
                        <x-text-input type="text" wire:model="name" name="name" />
                        @error('name')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                        <x-primary-button type="submit" wire:loading.remove>{{ __('Save') }}</x-primary-button>
                        <x-primary-button wire:loading>
                            <span role="status">{{ __('Saving') . '...' }}</span>
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
