<?php

use Livewire\Volt\Component;
use App\Models\Analysis;
use App\Models\Project;

new class extends Component
{
    public $projectId;
    public $analysisId;
    public $vulnerabilitiesCount;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $project = Project::withCount('vulnerabilities')->findOrFail($this->projectId);
        if ($project) {
            $this->analysisId = $project->latestAnalysis->id ?? null;
            $this->vulnerabilitiesCount = $project->vulnerabilities_count ?? 0;
        }
    }

}; ?>

<div class="d-flex flex-wrap align-items-center">
    <ul class="nav me-auto">
        <x-nav-link :href="route('projects.analysis', ['projectId' => $projectId])" :active="request()->routeIs('projects.analysis', ['projectId' => $projectId])" wire:navigate>
            <i class="fa-solid fa-crosshairs pe-1"></i>
            {{ __('Analysis') }}
        </x-nav-link>
        @if ($analysisId)
        <x-nav-link :href="route('projects.vulnerabilities', ['projectId' => $projectId])" :active="request()->routeIs('projects.vulnerabilities', ['projectId' => $projectId])" wire:navigate>
            <i class="fa-solid fa-triangle-exclamation pe-1"></i>
            {{ __('Vulnerabilities') }}
        </x-nav-link>
        @endif
        @if ($vulnerabilitiesCount)
        <x-nav-link :href="route('projects.export', ['projectId' => $projectId])" :active="request()->routeIs('projects.export', ['projectId' => $projectId])" wire:navigate>
            <i class="fa-solid fa-file-export pe-1"></i>
            {{ __('Export') }}
        </x-nav-link>
        @endif
    </ul>
</div>
