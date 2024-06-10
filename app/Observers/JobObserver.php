<?php

namespace App\Observers;

use App\Models\Job;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class JobObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Job "created" event.
     */
    public function created(Job $job): void
    {
        $job->analysis_id = session('analysis_id');
        $job->file_id = session('file_id');
        $job->save();
        session()->forget(['analysis_id', 'file_id']);
    }

    /**
     * Handle the Job "updated" event.
     */
    public function updated(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "deleted" event.
     */
    public function deleted(Job $job): void
    {
        //
    }
}
