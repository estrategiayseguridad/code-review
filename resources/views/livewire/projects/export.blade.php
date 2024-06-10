<?php

use Livewire\Volt\Component;
use App\Models\Project;

new class extends Component
{
    public $project;

    public function mount($projectId)
    {
        $this->project = Project::with(['vulnerabilities', 'vulnerabilities.file'])->findOrFail($projectId);
    }

}; ?>

@section('header')
    <livewire:layout.header-project :projectId="$project->id" />
@endsection

<div class="mb-5">


    <x-card>
        <x-slot:heading class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="fw-bold">{{ __('Vulnerabilities') }}</div>
            <x-primary-button type="button" class='btn btn-sm' onclick="exportReportToExcel()">
                <span><i class="fa-solid fa-file-export pe-1"></i>{{ __('Export') }}</span>
            </x-primary-button>
        </x-slot>
        <x-slot:body>
            <x-table id="table">
                <x-slot:head>
                    <tr class="align-middle">
                        <th scope="col">#</th>
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
                        <th>Herramienta</th>
                    </tr>
                </x-slot>
                <x-slot:body>
                    @foreach ($project->vulnerabilities->whereNotNull('verified_at') as $vulnerability)
                        <tr class="align-middle">
                            <th scope="row">{{ $loop->iteration }}</th>
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
                            <td>{{ $vulnerability->method }}</td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-table>
        </x-slot>
    </x-card>
</div>

@assets
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('js/FileSaver.min.js') }}"></script>
@endassets

<script>
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
        var view = new Uint8Array(buf); //create uint8array as viewer
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
        return buf;
    }
    function exportReportToExcel() {
        var wb = XLSX.utils.table_to_book(document.getElementById('table'), {sheet: 'Hoja 1', display: true});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', bookSST: true, type: 'binary'});
        saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'Reporte.xlsx');
    }
</script>
