<?php

use App\Models\Extension;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public Extension $extension;

    public $suffix = '';

    public function mount($extensionId): void
    {
        $this->extension = Extension::with('language')->find($extensionId);
        if ($this->extension) {
            $this->suffix = $this->extension->suffix;
        } else {
            $this->redirectRoute(url()->previous());
        }
    }

    public function save(): void
    {
        $this->validate([
            'suffix' => [
                'required',
                'string',
                Rule::unique('extensions')->ignore($this->extension),
            ]
        ]);
        $this->extension->suffix = $this->suffix;
        $this->extension->save();
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8">

            <div class="card">
                <div class="card-body">
                    <form wire:submit="save" class="d-grid gap-3">
                        <x-input-label for="suffix" :value="__('Extension')" />
                        <x-text-input type="text" wire:model="suffix" name="suffix" required />
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
