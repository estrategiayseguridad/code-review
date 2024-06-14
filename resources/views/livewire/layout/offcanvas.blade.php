<?php

use App\Models\Job;
use App\Models\FailedJob;
use Livewire\Attributes\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public $jobs;
    public $failed;
    public $open;

    public function mount(): void
    {
        $this->refreshData();
        $this->open = false;
    }

    public function refreshData(): void
    {
        $this->open = true;
        $this->jobs = Job::with(['file', 'file.project'])->orderByDesc('available_at')->get();
        $this->failed = FailedJob::orderByDesc('failed_at')->get();
    }

    public function retry(): void
    {
        Artisan::call('queue:retry all');
    }

    public function recall(): void
    {
        Artisan::call('queue:work --stop-when-empty');
    }

}; ?>

<div class="offcanvas offcanvas-start {{ $open ? 'show' : '' }}" tabindex="-1" id="offcanvas" wire:poll.5s.visible='refreshData'>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ __('Jobs') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

        <div class="d-flex justify-content-between align-items-center">
            <h4>En cola</h4>
            <button
                type="button"
                class="btn btn-transparent link-secondary"
                wire:click="recall">
                <i class="fa-solid fa-rotate-right pe-1"></i>{{ __('Retry') }}
            </button>
        </div>
        <x-table class="table-striped small mb-5">
            <x-slot:body>
                @forelse($jobs as $job)
                <tr wire:key="{{ $job->id }}">
                    <th scope="row" class="fit">
                        {{ $loop->iteration }}
                    </th>
                    <td class="d-grid">
                        <span class="small opacity-50">{{ $job->file->project->name }}</span>
                        <span>{{ basename($job->file->path) }}</span>
                        <span class="small opacity-50">{{ $job->file->path }}</span>
                        <span class="small opacity-50">{{ 'disponible: ' . $job->available_at }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">{{ __('No Content') }}</td>
                </tr>
                @endforelse
            </x-slot>
        </x-table>

        <div class="d-flex justify-content-between align-items-center">
            <h4>Fallidas</h4>
            <button
                type="button"
                class="btn btn-transparent link-secondary"
                wire:click="retry">
                <i class="fa-solid fa-rotate-right pe-1"></i>{{ __('Retry') }}
            </button>
        </div>
        <x-table class="table-striped small">
            <x-slot:body>
                @forelse($failed as $job)
                <tr wire:key="{{ $job->id }}">
                    <th scope="row" class="fit">
                        {{ $loop->iteration }}
                    </th>
                    <td>{{ $job->payload }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">{{ __('No Content') }}</td>
                </tr>
                @endforelse
            </x-slot>
        </x-table>

    </div>
</div>
