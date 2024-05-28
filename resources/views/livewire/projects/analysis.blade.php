<?php

use Livewire\Volt\Component;
use App\Models\Analysis;
use App\Models\File;
use App\Models\Project;
use App\Models\Vulnerability;

new class extends Component
{
    public $tab;
    public $project;
    public $files;
    public $vulnerabilities;
    public $progress;
    public $lastAnalysis;

    public function mount($projectId): void
    {
        $this->tab = 'files';
        $this->lastAnalysis = Project::findOrFail($projectId)->lastAnalysis();
        $this->refreshData($projectId);
    }

    public function showFile($file_id): void
    {
        $file = File::findOrfail($file_id);
        if ($file) {
            $contents = \File::get(storage_path('app/projects/' . $file->path));
            dd($contents);
        }
    }

    public function confirm($vulnerabilityId): void
    {
        $vulnerability = Vulnerability::findOrfail($vulnerabilityId);
        if ($vulnerability) {
            $vulnerability->verified_by = $vulnerability->verified_by ? null : auth()->user()->id;
            $vulnerability->verified_at = $vulnerability->verified_at ? null : now();
            $vulnerability->save();
            session()->flash('status', 'Vulnerability successfully updated.');
        }
    }

    public function analyze($projectId): void
    {
        $analysis = new Analysis([
            'project_id' => $projectId,
            'method' => 'Gemini API',
            'jobs' => 5,
            'created_by' => auth()->user()->id
        ]);
        $analysis->save();
    }

    public function refreshData($projectId): void
    {
        $this->project = Project::with(['files', 'vulnerabilities', 'files.vulnerabilities'])->findOrFail($projectId);
        $this->files = $this->project->files;
        $this->vulnerabilities = $this->project->vulnerabilities;
        $this->progress = $this->project->progress();
    }

}; ?>

<section>
    <div class="container" wire:poll.30s='refreshData({{ $project->id }})'>

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
                        @if ($lastAnalysis && $lastAnalysis->jobs > 0)
                        <x-light-button class='btn-sm' disabled>
                            <span class="spinner-border spinner-border-sm pe-1" aria-hidden="true"></span>
                            Analizando...
                        </x-light-button>
                        @else
                        <x-light-button class='btn-sm' wire:click="analyze({{ $project->id }})" wire:loading.attr="disabled">
                            <div wire:loading.remove wire:target="analyze({{ $project->id }})">
                                <i class="fa-solid fa-crosshairs pe-1"></i>
                                {{ __('Analyze') }}
                            </div>
                            <div wire:loading wire:target="analyze({{ $project->id }})">
                                <span wire:loading class="spinner-border spinner-border-sm pe-1" aria-hidden="true"></span>
                                Analizando...
                            </div>
                        </x-light-button>
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
                    <div class="col-12 col-md-3 border py-2">
                        <h1>{{ count($project->vulnerabilities) }}</h1>
                        <span><i class="fa-solid fa-triangle-exclamation pe-1"></i>{{ __('Vulnerabilities') }}</span>
                    </div>
                    <div class="col-4 col-md-3 border py-2">
                        <h1>{{ count($project->vulnerabilities->where('severity', 'Alta')) }}</h1>
                        <span><i class="fa-solid fa-temperature-full pe-1"></i>{{ __('Alta') }}</span>
                    </div>
                    <div class="col-4 col-md-3 border py-2">
                        <h1>{{ count($project->vulnerabilities->where('severity', 'Media')) }}</h1>
                        <span><i class="fa-solid fa-temperature-half pe-1"></i>{{ __('Media') }}</span>
                    </div>
                    <div class="col-4 col-md-3 border py-2">
                        <h1>{{ count($project->vulnerabilities->where('severity', 'Baja')) }}</h1>
                        <span><i class="fa-solid fa-temperature-quarter pe-1"></i> {{ __('Baja') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-primary alert-dismissible" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header p-0 border-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tab == 'files' ? 'active' : '' }}" wire:click="$set('tab', 'files')" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-tab-pane" type="button" role="tab" aria-controls="files-tab-pane" aria-selected="true">
                            {{ __('Files') . ' (' . count($project->files) . ')' }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tab == 'vulnerabilities' ? 'active' : '' }}" wire:click="$set('tab', 'vulnerabilities')" id="vulnerabilities-tab" data-bs-toggle="tab" data-bs-target="#vulnerabilities-tab-pane" type="button" role="tab" aria-controls="vulnerabilities-tab-pane" aria-selected="true">
                            {{ __('Vulnerabilities') . ' (' . count($project->vulnerabilities) . ')' }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0 rounded-0">
                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade {{ $tab == 'files' ? 'show active' : '' }}" id="files-tab-pane" role="tabpanel" aria-labelledby="files-tab" tabindex="0">
                        <div class="list-group d-grid m-0 mw-100 rounded-0">
                            @foreach ($files->whereNull('response') as $file)
                                <li wire:key="{{ $file->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border text-primary {{ $file->analyzed_at ? '' : 'd-none' }}" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <i class="fa-regular fa-2x fa-circle-pause text-body {{ $file->analyzed_at ? 'd-none' : '' }}"></i>
                                        <div class="ms-3">
                                            {{ basename($file->path) }}
                                            <span class="d-block small opacity-50">{{ $file->path }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            @foreach ($files->whereNotNull('response') as $file)
                                <li wire:key="{{ $file->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-regular fa-2x me-3 fa-circle-check text-success"></i>
                                        <div>
                                            {{ basename($file->path) }}
                                            @if (count($file->vulnerabilities))
                                                <span class="badge text-bg-danger rounded-pill ms-1">{{ count($file->vulnerabilities) }}</span>
                                            @endif
                                            <span class="d-block small opacity-50">{{ $file->path }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane p-3 fade {{ $tab == 'vulnerabilities' ? 'show active' : '' }}" id="vulnerabilities-tab-pane" role="tabpanel" aria-labelledby="vulnerabilities-tab" tabindex="0">

                        <div class="text-end mb-3">
                            <x-light-button class='btn-sm' wire:click="exportReportToExcel(this)">
                                <span><i class="fa-solid fa-file-export pe-1"></i>{{ __('Export') }}</span>
                            </x-light-button>
                        </div>

                        <div class="table-responsive table-bordered">
                            <table id="table" class="table mb-0">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vulnerabilities as $index => $vulnerability)
                                        @php $vulnerability->load('file'); @endphp
                                        <tr class="align-middle">
                                            <th scope="row">{{ $index + 1 }}</th>
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
</section>

<script type="text/javascript">
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
        var view = new Uint8Array(buf); //create uint8array as viewer
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
        return buf;
    }
    function exportReportToExcel() {
        hideUncheckedRows(true);
        var wb = XLSX.utils.table_to_book(document.getElementById('table'), {sheet: 'Hoja 1', display: true});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', bookSST: true, type: 'binary'});
        saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'Reporte.xlsx');
        hideUncheckedRows(false);
    }
    function hideUncheckedRows(hide) {
        const ignoreElements = document.querySelectorAll('.ignore');
        ignoreElements.forEach(element => {
            element.style.display = hide ? 'none' : '';
        });
        const uncheckedCheckboxes = document.querySelectorAll('.form-check-input:not(:checked)');
        uncheckedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            row.style.display = hide ? 'none' : '';
        });
    }
    function updateSpinner(percentage, timing) {
        setArc({target: {value: percentage, timing: timing}});
    }
</script>
