<?php

use App\Models\Parameter;
use App\Models\Project;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Database\Eloquent\Builder;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';

    public function render(): mixed
    {
        return view('livewire.projects.index', [
                'pagination' => Project::with(['owner', 'analyses', 'vulnerabilities'])->when($this->search !== '', fn(Builder $query) => $query->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(Parameter::val('PAGINATION_ROWS_PER_PAGE'))
            ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function showFiles($projectId): void
    {
        $this->redirectRoute('projects.analysis', ['projectId' => $projectId]);
    }

}; ?>

@section('header')
    <livewire:layout.header-projects />
@endsection

<div>
    <div class="row justify-content-end mb-3">
        <div class="col-12 col-md-3">
            <x-search-input />
        </div>
    </div>
    <div class="list-group mb-4">
        @forelse ($pagination->items() as $project)
            <button
                wire:key="{{ $project->id }}"
                wire:click="showFiles({{ $project->id }})"
                class="list-group-item list-group-item-action text-bg-dark p-4">
                <div class="row d-flex align-items-center gy-2">
                    <div class="col-12 col-md-auto">
                        <i class="fa-regular fa-folder-open fa-2x"></i>
                    </div>
                    <div class="col-12 col-md">
                        <h4>{{ $project->name }}</h4>
                        @foreach ($project->stats() as $key => $value)
                            <span class="badge text-bg-dark border">{{ "{$key} {$value}%" }}</span>
                        @endforeach
                    </div>
                    <div class="col-12 col-md-auto">
                        <p class="mb-0 opacity-75">
                            <span class="fw-bold fs-4 pe-2">{{ count($project->vulnerabilities) }}</span>
                            {{ __('Vulnerabilities') }}
                        </p>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="row">
                            <div class="col-auto pe-0">
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 fw-bold text-danger">Crítica</span>
                                    <span class="input-group-text rounded-0">{{ count($project->vulnerabilities->where('severity', 'Crítica')) }}</span>
                                </div>
                            </div>
                            <div class="col-auto pe-0">
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 fw-bold text-danger">Alta</span>
                                    <span class="input-group-text rounded-0">{{ count($project->vulnerabilities->where('severity', 'Alta')) }}</span>
                                </div>
                            </div>
                            <div class="col-auto pe-0">
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 fw-bold text-warning">Media</span>
                                    <span class="input-group-text rounded-0">{{ count($project->vulnerabilities->where('severity', 'Media')) }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 fw-bold text-body">Baja</span>
                                    <span class="input-group-text rounded-0">{{ count($project->vulnerabilities->where('severity', 'Baja')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </button>
        @empty
            <div class="border rounded p-3">{{ __('No Content') }}</div>
        @endforelse
    </div>
    {{ $pagination->links(data: ['scrollTo' => false]) }}
</div>
