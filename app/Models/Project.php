<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'directory',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'project_language');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function lastAnalysis()
    {
        return $this->hasMany(Analysis::class)->latest()->first();
    }

    public function vulnerabilities(): HasManyThrough
    {
        return $this->hasManyThrough(Vulnerability::class, Analysis::class);
    }

    public function progress()
    {
        $progress = 0;
        $files_count = $this->files()->count();
        $analyses_count = $this->files()->whereNotNull('analyzed_at')->count();
        $progress = $files_count === 0 ? 0 : (number_format($analyses_count / $files_count * 100, 0));
        return $progress;
    }

    public function stats()
    {
        $stats = array();
        if ($this->languages) {
            $files_count = $this->files()->count();
            foreach ($this->languages as $language) {
                $language->load('extensions');
                $language_count = $this->files()->whereIn('extension_id', $language->extensions->pluck('id')->toArray())->count();
                $stats[$language->name] = number_format($language_count / $files_count * 100, 1);
            }
        }
        arsort($stats);
        return $stats;
    }
}
