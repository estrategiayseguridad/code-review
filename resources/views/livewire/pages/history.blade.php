<?php

use App\Models\Event;
use App\Models\Parameter;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Volt\Component;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $page;

    public function render(): mixed
    {
        return view('livewire.pages.history', [
            'pagination' => Event::with('user')->orderByDesc('created_at')->paginate(Parameter::val('PAGINATION_ROWS_PER_PAGE'))
        ]);
    }

    public function truncate(): void
    {
        \DB::table('events')->truncate();
        $this->resetPage();
    }

    public function refreshData(): void
    {
        $this->setPage($this->page);
    }

    public function updatingPage($page)
    {
        // Runs before the page is updated
        $this->page = $page;
    }

}; ?>

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <x-card>
            <x-slot:heading>{{ __('Events') }}</x-slot>
            <x-slot:body wire:poll.5s='refreshData'>
                <x-table class="table-striped">
                    <x-slot:body>
                        @if ($pagination)
                            @forelse($pagination->items() as $event)
                            <tr wire:key="{{ $event->id }}">
                                <td>{{ $event->created_at }}</td>
                                <td>{{ $event->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">{{ __('No Content') }}</td>
                            </tr>
                            @endforelse
                        @endif
                    </x-slot>
                </x-table>
                {{ $pagination->links(data: ['scrollTo' => false]) }}
                <div class="d-flex">
                    <x-danger-button
                        type="button"
                        wire:click="truncate"
                        wire:confirm="¿Desea continuar con la eliminación?">
                            {{ __('Delete All') }}
                    </x-danger-button>
                </div>
            </x-slot>
        </x-card>
    </div>
</div>

