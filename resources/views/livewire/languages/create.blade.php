<?php

use App\Models\Language;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    #[Rule('required|string|unique:languages,name', as: 'Nombre')]
    public $name = '';

    public function save(): void
    {
        Language::create($this->validate());
        $this->redirectRoute('languages.index');
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-5">
            <div class="card">
                <div class="card-header">{{ __('Language') }}</div>
                <div class="card-body">
                    <form wire:submit="save" class="d-grid gap-3">
                        <x-text-input type="text" wire:model="name" name="name" required />
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
