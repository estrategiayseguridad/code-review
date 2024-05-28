<?php

use Livewire\Volt\Component;
use App\Models\Project;

new class extends Component
{
    public $projects;

    public function mount(): void
    {
        $this->projects = Project::with(['owner', 'analyses', 'vulnerabilities'])->get();
    }

    public function showFiles($projectId): void
    {
        $this->redirectRoute('projects.analysis', ['projectId' => $projectId]);
    }

}; ?>

<section>
    <div class="container">
        <div class="text-end mb-4">
            <a class="btn btn-sm btn-light" href="{{ route('projects.new') }}">
                <i class="fa-solid fa-plus pe-2"></i>{{ __('New') }}
            </a>
        </div>
        <div class="list-group mx-0 d-inline">
            @foreach ($this->projects as $project)
                <button
                    wire:key="{{ $project->id }}"
                    wire:click="showFiles({{ $project->id }})"
                    class="list-group-item list-group-item-action p-4">
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
            @endforeach
        </div>
    </div>
</div>
