<?php

use App\Models\Extension;
use App\Models\Language;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public Language $language;
    public $extensions;
    public $name = '';

    public function mount($languageId): void
    {
        $this->language = Language::with('extensions')->find($languageId);
        if ($this->language) {
            $this->extensions = $this->language->extensions;
            $this->name = $this->language->name;
        } else {
            $this->redirectRoute('languages.index');
        }
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('languages')->ignore($this->language),
            ]
        ];
    }

    public function save(): void
    {
        $this->validate();
        $this->language->name = $this->name;
        $this->language->save();
    }

    public function editExtension($extensionId): void
    {
        $this->redirectRoute('extensions.edit', ['extensionId' => $extensionId]);
    }

    public function deleteExtension($extensionId): void
    {
        Extension::find($extensionId)->delete();
        $this->language = Language::with('extensions')->find($this->language->id);
        $this->extensions = $this->language->extensions;
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8">

            <div class="card">
                <div class="card-header">
                    {{ __('Language') }}
                </div>
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

            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>{{ __('Extensions') }}</div>
                        <a class="btn btn-sm btn-light" href="{{ route('extensions.create', ['languageId' => $language->id]) }}" wire:navigate>
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                @foreach($extensions as $extension)
                                <tr wire:key="{{ $extension->id }}">
                                    <th scope="row">{{ $loop->iteration }}</th>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
