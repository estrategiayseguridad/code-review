<?php

namespace App\Models;

use App\Observers\ExtensionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([ExtensionObserver::class])]
class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'suffix',
        'language_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
