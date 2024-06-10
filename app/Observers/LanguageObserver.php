<?php

namespace App\Observers;

use App\Models\Language;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class LanguageObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Language "created" event.
     */
    public function created(Language $language): void
    {
        $user = auth()->user();
        if ($user) {
            $user->events()->create([
                'type' => 'created',
                'description' => "Usuario {$user->username} ha creado el lenguaje {$language->name} con ID:{$language->id}."
            ]);
        }
    }

    /**
     * Handle the Language "updated" event.
     */
    public function updated(Language $language): void
    {
        //
    }

    /**
     * Handle the Language "deleted" event.
     */
    public function deleted(Language $language): void
    {
        $user = auth()->user();
        if ($user) {
            $user->events()->create([
                'type' => 'deleted',
                'description' => "Usuario {$user->username} ha eliminado el lenguaje {$language->name} con ID:{$language->id}."
            ]);
        }
    }
}
