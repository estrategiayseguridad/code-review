<?php

use App\Models\Extension;
use App\Models\Language;
use Illuminate\Validation\Validate;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public ?Language $language;
    public $extensions;
    public $name;

    public $extensionId;

    #[Validate('required|string|unique:extensions,suffix', as: 'Extensión')]
    public $suffix;

    public function mount($languageId): void
    {
        $this->language = Language::with('extensions')->findOrFail($languageId);
        if ($this->language) {
            $this->extensions = $this->language->extensions;
            $this->name = $this->language->name;
        } else {
            $this->redirectRoute('languages.index');
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('languages')->ignore($this->language),
            ]
        ]);
        $this->language->name = $this->name;
        $this->language->save();
        $this->dispatch('language-updated');
    }

    public function saveExtension(): void
    {
        $extension = Extension::updateOrCreate(
            ['id' => $this->extensionId],
            [
                'suffix' => $this->suffix,
                'language_id' => $this->language->id
            ]
        );
        if ($extension) {
            $this->extensionId = null;
            $this->suffix = null;
            $this->extensions = Extension::where('language_id', $this->language->id)->get();
            $this->dispatch('extension-saved');
        }
    }

    public function editExtension($extensionId): void
    {
        $extension = Extension::findOrFail($extensionId);
        if ($extension) {
            $this->extensionId = $extension->id;
            $this->suffix = $extension->suffix;
        }
    }

    public function deleteExtension($extensionId): void
    {
        Extension::find($extensionId)->delete();
        $this->language = Language::with('extensions')->find($this->language->id);
        $this->extensions = $this->language->extensions;
    }

}; ?>

@section('header')
    <livewire:layout.header-languages />
@endsection

<div class="row justify-content-center">
    <div class="col-10 col-md-8">

        <x-card>
            <x-slot:heading>
                {{ __('Language') }}
                <div class="text-lowercase float-end">
                    <x-action-message on="language-updated">{{ __('Saved.') }}</x-action-message>
                </div>
            </x-slot>
            <x-slot:body>
                {{-- Update Language Form --}}
                <form wire:submit="save" class="d-flex gap-2">
                    <x-text-input type="text" wire:model="name" name="name" required />
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                    <x-primary-button type="submit" wire:loading.remove wire:target='save'>
                        {{ __('Save') }}
                    </x-primary-button>
                    <x-primary-button wire:loading wire:target='save'>
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    </x-primary-button>
                </form>
            </x-slot>
        </x-card>

        <x-card class="mt-4">
            <x-slot:heading>
                {{ __('Extensions') }}
                <div class="text-lowercase float-end">
                    <x-action-message on="extension-saved">{{ __('Saved.') }}</x-action-message>
                </div>
            </x-slot>
            <x-slot:body>

                {{-- Create Extension Form --}}
                <form wire:submit="saveExtension" class="d-flex gap-2">
                    <x-text-input type="hidden" wire:model="extensionId" />
                    <x-text-input type="text" wire:model="suffix" name="suffix" placeholder="ejemplo: .php" required />
                    @error('suffix')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                    <x-primary-button wire:loading.remove wire:target='saveExtension'>{{ __('Save') }}</x-primary-button>
                    <x-primary-button wire:loading wire:target='saveExtension'>
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    </x-primary-button>
                </form>

                <hr/>

                {{-- List --}}
                <x-table class="table-striped">
                    <x-slot:body>
                        @forelse($extensions as $extension)
                        <tr wire:key="{{ $extension->id }}">
                            <td>{{ $extension->suffix }}</td>
                            <td class="fit">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light me-2"
                                    wire:click="editExtension({{ $extension->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light"
                                    wire:click="deleteExtension({{ $extension->id }})"
                                    wire:confirm="¿Desea continuar con la eliminación?">
                                        <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2">{{ __('No Content') }}</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-table>

            </x-slot>
        </x-card>

    </div>
</div>
