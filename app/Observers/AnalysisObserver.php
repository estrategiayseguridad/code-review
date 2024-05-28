<?php

namespace App\Observers;

use App\Models\Analysis;
use App\Models\File;
use App\Jobs\ProcessAnalysis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class AnalysisObserver
{
    /**
     * Handle the Analysis "created" event.
     */
    public function created(Analysis $analysis): void
    {
        $files = File::where('project_id', $analysis->project_id)->whereNull('response')->get();
        if ($analysis && $files) {
            foreach ($files as $index => $file) {
                if ($index == $analysis->jobs) {
                    break;
                }
                if (Storage::disk('projects')->exists($file->path)) {
                    ProcessAnalysis::dispatch($analysis->id, $file->id)->delay(now()->addSeconds($index * 40));
                }
            }
            Artisan::call('queue:work --stop-when-empty');
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
        //
    }

    /**
     * Handle the Analysis "restored" event.
     */
    public function restored(Analysis $analysis): void
    {
        //
    }

    /**
     * Handle the Analysis "force deleted" event.
     */
    public function forceDeleted(Analysis $analysis): void
    {
        //
    }
}
