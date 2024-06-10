<?php

namespace App\Queue;

use Illuminate\Queue\DatabaseQueue;
use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Queue\Jobs\DatabaseJob;
use Illuminate\Queue\Jobs\DatabaseJobRecord;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Auth;

class CustomDatabaseQueue extends DatabaseQueue
{
    /**
     * Create an array to insert for the given job.
     * @Note:- Overriding to add custom fields
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  int  $availableAt
     * @param  int  $attempts
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        return [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => $availableAt,
            'created_at' => $this->currentTime(),
            'payload' => $payload,
            'analysis_id' => session('analysis_id'),
            'file_id' => session('file_id'),
        ];
    }
}
