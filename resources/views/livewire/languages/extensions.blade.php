<?php

use App\Models\Extension;
use App\Models\Parameter;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validate;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search;
    public $showForm;
    public $extensionId;

    #[Validate('required|string|unique:extensions,suffix', as: 'Extensión')]
    public $suffix;

    public function resetData(): void
    {
        $this->search = '';
        $this->suffix = '';
        $this->extensionId = null;
        $this->showForm = false;
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('livewire.languages.extensions', [
            'pagination' => Extension::with('language')->when($this->search !== '', fn(Builder $query) => $query->where('suffix', 'like', "%{$this->search}%"))->orderBy('suffix')->paginate(Parameter::val('PAGINATION_ROWS_PER_PAGE'))
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $extension = Extension::findOrFail($this->extensionId);

        if ($extension && $extension->update(['suffix' => $this->suffix])) {
            $this->resetData();
        }
    }

    public function delete($extensionId): void
    {
        $extension = Extension::findOrFail($extensionId);

        if ($extension->delete()) {
            $this->resetData();
        }
    }

    public function edit($extensionId): void
    {
        $extension = Extension::findOrFail($extensionId);
        if ($extension) {
            $this->extensionId = $extension->id;
            $this->suffix = $extension->suffix;
            $this->showForm = true;
        }
    }

}; ?>

@section('header')
    <livewire:layout.header-languages />
@endsection

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <x-card>
            <x-slot:heading>{{ __('Extensions') }}</x-slot>
            <x-slot:body>

                @if ($showForm)
                <div wire:transition>
                    {{-- Update Form --}}
                    <form wire:submit="save" class="d-flex gap-3">
                        <x-text-input type="hidden" wire:model="extensionId" />
                        <x-text-input type="text" wire:model="suffix" name="suffix" required />
                        @error('suffix')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                        <x-primary-button wire:loading.remove wire:target='save'>{{ __('Save') }}</x-primary-button>
                        <x-primary-button wire:loading wire:target='save'>
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        </x-primary-button>
                        <x-primary-button type="button" wire:click="$set('showForm', false)">{{ __('Cancel') }}</x-primary-button>
                    </form>
                    <hr />
                </div>
                @endif

                {{-- Search Box --}}
                <x-search-input />

                {{-- List --}}
                <x-table class="table-striped">
                    <x-slot:body>
                        @forelse($pagination->items() as $extension)
                        <tr wire:key="{{ $extension->id }}">
                            <th scope="row" class="fit">
                                {{ $pagination->firstItem() + $loop->index }}
                            </th>
                            <td>{{ $extension->suffix }}</td>
                            <td>{{ $extension->language->name }}</td>
                            <td class="fit">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light me-2"
                                    wire:click="edit({{ $extension->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light"
                                    wire:click="delete({{ $extension->id }})"
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
