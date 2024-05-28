<?php

use App\Models\Language;
use Livewire\Volt\Component;

new class extends Component
{
    public $languages;

    public function mount(): void
    {
        $this->languages = Language::orderBy('name')->get();
    }

    public function edit($languageId): void
    {
        $this->redirectRoute('languages.edit', ['languageId' => $languageId]);
    }

    public function delete($languageId): void
    {
        Language::find($languageId)->delete();
        $this->languages = Language::orderBy('name')->get();
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8">

            <div class="d-grid">
                <a class="btn btn-sm btn-light" href="{{ route('languages.create') }}" wire:navigate>
                    <i class="fa-solid fa-plus pe-2"></i>{{ __('New') }}
                </a>
            </div>

            <div class="mt-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            @foreach($languages as $language)
                            <tr wire:key="{{ $language->id }}">
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $language->name }}</td>
                                <td class="fit">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-light me-2"
                                        wire:click="edit({{ $language->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-light"
                                        wire:click="delete({{ $language->id }})"
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
