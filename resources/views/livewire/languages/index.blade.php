<?php

use App\Models\Language;
use App\Models\Parameter;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Database\Eloquent\Builder;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    #[Validate('required|string|unique:languages,name', as: 'Nombre')]
    public $name = '';

    public $search = '';

    public function resetData(): void
    {
        $this->name = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('livewire.languages.index', [
            'pagination' => Language::when($this->search !== '', fn(Builder $query) => $query->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(Parameter::val('PAGINATION_ROWS_PER_PAGE'))
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        if (Language::create($this->validate())) {
            $this->resetData();
        }
    }

    public function delete($languageId): void
    {
        $language = Language::findOrFail($languageId);

        if ($language->delete()) {
            $this->resetData();
        }
    }

    public function edit($languageId): void
    {
        $this->redirectRoute('languages.edit', ['languageId' => $languageId], navigate: true);
    }

}; ?>

@section('header')
    <livewire:layout.header-languages />
@endsection

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <x-card>
            <x-slot:heading>{{ __('Language') }}</x-slot>
            <x-slot:body>

                {{-- Create Form --}}
                <form wire:submit="save" class="d-flex gap-3">
                    <x-text-input type="text" wire:model="name" name="name" required />
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                    <x-primary-button type="submit" wire:loading.remove wire:target='save'>{{ __('Add') }}</x-primary-button>
                    <x-primary-button wire:loading wire:target='save'>
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    </x-primary-button>
                </form>

                <hr/>

                {{-- Search Box --}}
                <x-search-input />

                {{-- List --}}
                <x-table class="table-striped">
                    <x-slot:body>
                        @forelse($pagination->items() as $language)
                        <tr wire:key="{{ $language->id }}">
                            <th scope="row" class="fit">
                                {{ $pagination->firstItem() + $loop->index }}
                            </th>
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
                        @empty
                        <tr>
                            <td colspan="3">{{ __('No Content') }}</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-table>

                {{ $pagination->links(data: ['scrollTo' => false]) }}

            </x-slot>
        </x-card>
    </div>
</div>
