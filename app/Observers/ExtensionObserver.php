<?php

namespace App\Observers;

use App\Models\Extension;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ExtensionObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Extension "created" event.
     */
    public function created(Extension $extension): void
    {
        $user = auth()->user();
        if ($user) {
            $extension->load('language');
            $user->events()->create([
                'type' => 'created',
                'description' => "Usuario {$user->username} ha creado la extensión {$extension->suffix} con ID:{$extension->id} para el lenguaje {$extension->language->name} ID:{$extension->language->id}."
            ]);
        }
    }

    /**
     * Handle the Extension "updated" event.
     */
    public function updated(Extension $extension): void
    {
        //
    }

    /**
     * Handle the Extension "deleted" event.
     */
    public function deleted(Extension $extension): void
    {
        $user = auth()->user();
        if ($user) {
            $extension->load('language');
            $user->events()->create([
                'type' => 'deleted',
                'description' => "Usuario {$user->username} ha eliminado la extensión {$extension->suffix} con ID:{$extension->id} para el lenguaje {$extension->language->name} ID:{$extension->language->id}."
            ]);
        }
    }
}
