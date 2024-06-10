<?php

namespace App\Models;

use App\Observers\JobObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

#[ObservedBy([JobObserver::class])]
class Job extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'analysis_id',
        'file_id',
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at'
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'available_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function getAvailableAtAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
