<?php

use Livewire\Volt\Component;
use App\Models\Analysis;
use App\Models\Project;
use App\Models\Vulnerability;

new class extends Component
{
    public $projectId;
    public $project;
    public $vulnerabilities;
    public $showGemini;
    public $showOpenAI;

    public function mount($projectId): void
    {
        $this->projectId = $projectId;
        $this->showGemini = true;
        $this->showOpenAI = true;
        $this->refreshData();
    }

    public function confirm($vulnerabilityId): void
    {
        $vulnerability = Vulnerability::findOrFail($vulnerabilityId);
        if ($vulnerability) {
            $vulnerability->verified_by = $vulnerability->verified_by ? null : auth()->user()->id;
            $vulnerability->verified_at = $vulnerability->verified_at ? null : now();
            $vulnerability->save();
            $this->dispatch('vulnerability-updated');
            $this->refreshData();
        }
    }

    public function refreshData(): void
    {
        $this->project = Project::with(['vulnerabilities'])->findOrFail($this->projectId);
        $this->vulnerabilities = $this->project->vulnerabilities;
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

    <x-action-message class="m-1" on="vulnerability-updated">{{ __('Saved.') }}</x-action-message>

    <div class="accordion mb-4">
        <div class="accordion-item">
            <h2 class="accordion-header border-bottom">
                <button class="accordion-button text-body" type="button" wire:click="$toggle('showGemini')"
                    data-bs-toggle="collapse" data-bs-target="#panel-gemini" aria-expanded="true" aria-controls="panel-gemini">
                    Gemini API <span class="small opacity-50 ps-1">({{ number_format($project->latestAnalysis->gemini_tokens) }} tokens)</span>
                </button>
            </h2>
            <div class="accordion-collapse collapse {{ $showGemini ? 'show' : '' }}" id="panel-gemini">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table name="table" class="table mb-0">
                            <thead>
                                <tr class="align-middle">
                                    <th scope="col">#</th>
                                    <th class="ignore"></th>
                                    <th>Archivo</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Líneas</th>
                                    <th>Severidad</th>
                                    <th>Impacto</th>
                                    <th>CWE</th>
                                    <th>CVE</th>
                                    <th>Solución</th>
                                    <th>Mitigación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vulnerabilities->where('method', 'GEMINI API') as $vulnerability)
                                    <tr class="align-middle">
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td class="ignore">
                                            <input type="checkbox" class="form-check form-check-input" wire:change="confirm({{ $vulnerability->id }})" {{ $vulnerability->verified_at ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $vulnerability->file->path }}</td>
                                        <td>{{ $vulnerability->name }}</td>
                                        <td>{{ $vulnerability->description }}</td>
                                        <td>{{ $vulnerability->lines }}</td>
                                        <td>{{ $vulnerability->severity }}</td>
                                        <td>{{ $vulnerability->impact }}</td>
                                        <td>{{ $vulnerability->cwe }}</td>
                                        <td>{{ $vulnerability->cve }}</td>
                                        <td>{{ $vulnerability->solution }}</td>
                                        <td>{{ $vulnerability->mitigation }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion mb-4">
        <div class="accordion-item">
            <h2 class="accordion-header border-bottom">
                <button class="accordion-button text-body" type="button" wire:click="$toggle('showOpenAI')"
                    data-bs-toggle="collapse" data-bs-target="#panel-openai" aria-expanded="true" aria-controls="panel-openai">
                    OpenAI API <span class="small opacity-50 ps-1">({{ number_format($project->latestAnalysis->openai_tokens) }} tokens)</span>
                </button>
            </h2>
            <div class="accordion-collapse collapse {{ $showOpenAI ? 'show' : '' }}" id="panel-openai">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table name="table" class="table mb-0">
                            <thead>
                                <tr class="align-middle">
                                    <th scope="col">#</th>
                                    <th class="ignore"></th>
                                    <th>Archivo</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Líneas</th>
                                    <th>Severidad</th>
                                    <th>Impacto</th>
                                    <th>CWE</th>
                                    <th>CVE</th>
                                    <th>Solución</th>
                                    <th>Mitigación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vulnerabilities->where('method', 'OPENAI API') as $vulnerability)
                                    <tr class="align-middle">
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td class="ignore">
                                            <input type="checkbox" class="form-check form-check-input" wire:change="confirm({{ $vulnerability->id }})" {{ $vulnerability->verified_at ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $vulnerability->file->path }}</td>
                                        <td>{{ $vulnerability->name }}</td>
                                        <td>{{ $vulnerability->description }}</td>
                                        <td>{{ $vulnerability->lines }}</td>
                                        <td>{{ $vulnerability->severity }}</td>
                                        <td>{{ $vulnerability->impact }}</td>
                                        <td>{{ $vulnerability->cwe }}</td>
                                        <td>{{ $vulnerability->cve }}</td>
                                        <td>{{ $vulnerability->solution }}</td>
                                        <td>{{ $vulnerability->mitigation }}</td>
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
