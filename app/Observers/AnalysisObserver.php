<?php

namespace App\Observers;

use App\Models\Analysis;
use App\Models\File;
use App\Models\Parameter;
use App\Jobs\ProcessAnalysis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class AnalysisObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Analysis "created" event.
     */
    public function created(Analysis $analysis): void
    {
        $user = auth()->user();
        $user->events()->create([
            'type' => 'created',
            'description' => "Usuario {$user->username} ha creado el análisis ID:{$analysis->id} para el proyecto ID:{$analysis->project_id}."
        ]);
        $files = File::where('project_id', $analysis->project_id)->whereNull('analyzed_at')->get();
        if ($analysis && $files) {
            $testing = Parameter::val('JOBS_FOR_TESTING');
            $interval = Parameter::val('JOBS_INTERVAL') ?? 40;
            foreach ($files as $index => $file) {
                if ($testing && $index == $testing) {
                    break;
                }
                if (Storage::disk('projects')->exists($file->path)) {
                    session(['analysis_id' => $analysis->id, 'file_id' => $file->id]);
                    ProcessAnalysis::dispatch($analysis->id, $file->id)->delay(now()->addSeconds($index * $interval));
                }
            }
            Artisan::call('queue:work --max-time=3600 --stop-when-empty');
        }
    }

    /**
     * Handle the Analysis "updated" event.
     */
    public function updated(Analysis $analysis): void
    {
        //
    }

    /**
     * Handle the Analysis "deleted" event.
     */
    public function deleted(Analysis $analysis): void
    {
        $user = auth()->user();
        $user->events()->create([
            'type' => 'deleted',
            'description' => "Usuario {$user->username} ha eliminado el análisis ID:{$analysis->id} del proyecto ID:{$analysis->project_id}."
        ]);
    }
}
