<?php

use App\Models\Extension;
use App\Models\Language;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public Language $language;

    #[Rule('required|string|unique:extensions,suffix', as: 'Extensión')]
    public $suffix = '';

    public function mount($languageId): void
    {
        $this->language = Language::find($languageId);
        if (is_null($this->language)) {
            $this->redirectRoute(url()->previous());
        }
    }

    public function save(): void
    {
        $extension = new Extension(['suffix' => $this->suffix]);
        $this->language->extensions()->save($extension);
        $this->redirectRoute('languages.edit', ['languageId' => $this->language->id]);
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-5">

            <div class="card">
                <div class="card-header">{{ __('Extension') }}</div>
                <div class="card-body">
                    <form wire:submit="save" class="d-grid gap-3">
                        <x-text-input type="text" wire:model="suffix" name="suffix" placeholder=".php" required />
                        @error('suffix')
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
