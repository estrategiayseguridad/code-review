<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Storage;

class ProjectObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        $user = auth()->user();
        $user->events()->create([
            'type' => 'created',
            'description' => "Usuario {$user->username} ha creado el proyecto {$project->name} con ID:{$project->id}."
        ]);
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        if (Storage::exists($project->directory)) {
            Storage::deleteDirectory($project->directory);
        }

        $user = auth()->user();
        $user->events()->create([
            'type' => 'deleted',
            'description' => "Usuario {$user->username} ha eliminado el proyecto {$project->name} con ID:{$project->id}."
        ]);
    }
}
