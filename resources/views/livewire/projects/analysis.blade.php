<?php

use Livewire\Volt\Component;
use App\Models\Analysis;
use App\Models\File;
use App\Models\Parameter;
use App\Models\Project;
use App\Models\Vulnerability;

new class extends Component
{
    public $project;
    public $files;
    public $progress;
    public $projectId;

    public function mount($projectId): void
    {
        $this->tab = 'files';
        $this->projectId = $projectId;
        $this->refreshData();
    }

    public function analyze(): void
    {
        if ((Parameter::val('GEMINI_API') === 'true' && is_null(Parameter::val('GEMINI_API_KEY'))) ||
            (Parameter::val('OPENAI_API') === 'true' && (is_null(Parameter::val('OPENAI_API_KEY')) || is_null(Parameter::val('OPENAI_ORGANIZATION'))))) {
            $this->redirectRoute('config.general');
        } else {
            Analysis::create([
                'project_id' => $this->projectId,
                'created_by' => auth()->user()->id
            ]);
        }
    }

    public function refreshData(): void
    {
        $this->project = Project::with(['vulnerabilities'])->findOrFail($this->projectId);
        $this->progress = $this->project->progress();
        $this->files = File::withCount(['jobs', 'vulnerabilities'])->where('project_id', $this->projectId)->take(100)->get();
    }

    public function delete(): void
    {
        $project = Project::findOrFail($this->projectId);

        if ($project->delete()) {
            $this->redirect('/', navigate: true);
        }
    }

}; ?>

@section('header')
    <livewire:layout.header-project :projectId="$projectId" />
@endsection

<div wire:poll.10s.visible='refreshData'>

    <div class="card mb-4">
        <div class="card-header text-center py-4">
            <h2>{{ strtoupper($project->name) }}</h2>
            <div>
                @foreach ($project->stats() as $key => $value)
                    <span class="badge border">{{ "{$key} {$value}%" }}</span>
                @endforeach
            </div>
        </div>
        <div class="card-body">
            <div class="row justify-content-between align-items-center gy-3">
                <div class="col-12 col-md-auto d-grid">
                    @if ($project->status === 'in progress')
                    <x-primary-button class='btn-sm' disabled>
                        <span class="spinner-border spinner-border-sm pe-1" aria-hidden="true"></span>
                        Analizando...
                    </x-primary-button>
                    @else
                    <x-primary-button class='btn-sm' wire:click="analyze" wire:loading.attr="disabled">
                        <div wire:loading.remove wire:target="analyze">
                            <i class="fa-solid fa-crosshairs pe-1"></i>
                            {{ __('Analyze') }}
                        </div>
                        <div wire:loading wire:target="analyze">
                            <span wire:loading class="spinner-border spinner-border-sm pe-1" aria-hidden="true"></span>
                            Analizando...
                        </div>
                    </x-primary-button>
                    @endif
                </div>
                <div class="col-12 col-md">
                    <div class="progress" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $progress }}%">{{ $progress }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row text-center m-0">
                <div class="col-12 col-md border py-2">
                    <h1>{{ count($project->vulnerabilities) }}</h1>
                    <span><i class="fa-solid fa-triangle-exclamation pe-1"></i>{{ __('Vulnerabilities') }}</span>
                </div>
                <div class="col-6 col-md border py-2">
                    <h1>{{ count($project->vulnerabilities->where('severity', 'Crítica')) }}</h1>
                    <span><i class="fa-solid fa-temperature-full pe-1"></i>{{ __('Crítica') }}</span>
                </div>
                <div class="col-6 col-md border py-2">
                    <h1>{{ count($project->vulnerabilities->where('severity', 'Alta')) }}</h1>
                    <span><i class="fa-solid fa-temperature-full pe-1"></i>{{ __('Alta') }}</span>
                </div>
                <div class="col-6 col-md border py-2">
                    <h1>{{ count($project->vulnerabilities->where('severity', 'Media')) }}</h1>
                    <span><i class="fa-solid fa-temperature-half pe-1"></i>{{ __('Media') }}</span>
                </div>
                <div class="col-6 col-md border py-2">
                    <h1>{{ count($project->vulnerabilities->where('severity', 'Baja')) }}</h1>
                    <span><i class="fa-solid fa-temperature-quarter pe-1"></i> {{ __('Baja') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-body p-0">

            <div class="list-group d-grid">
                @forelse ($files as $file)
                    <li wire:key="{{ $file->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if ($file->analyzed_at)
                                <i class="fa-regular fa-2x fa-circle-check text-primary"></i>
                            @else
                                @if ($file->jobs_count)
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                @else
                                    <i class="fa-regular fa-2x fa-circle-pause text-body"></i>
                                @endif
                            @endif
                            <div class="ms-3 me-auto">
                                {{ basename($file->path) }}
                                <span class="d-block small opacity-50">{{ $file->path }}</span>
                            </div>
                        </div>
                        @if ($file->vulnerabilities->count())
                        <div><span class="badge text-bg-danger rounded-circle">{{ $file->vulnerabilities_count }}</span></div>
                        @endif
                    </li>
                @empty
                @endforelse
            </div>
        </div>
    </div>

    <div class="text-center">
        <button
            type="button"
            class="btn btn-transparent link-danger"
            wire:click="delete"
            wire:confirm="¿Desea continuar con la eliminación?">
                {{ __('Delete') . ' ' . __('Project') }}
        </button>
    </div>

</div>
