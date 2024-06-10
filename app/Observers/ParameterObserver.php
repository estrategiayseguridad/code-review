<?php

namespace App\Observers;

use App\Models\Parameter;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ParameterObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Parameter "created" event.
     */
    public function created(Parameter $parameter): void
    {
        $user = auth()->user();
        if ($user) {
            $user->events()->create([
                'type' => 'created',
                'description' => "Usuario {$user->username} ha creado el parámetro {$parameter->key}:{$parameter->value} con ID:{$parameter->id}."
            ]);
        }
    }

    /**
     * Handle the Parameter "updated" event.
     */
    public function updated(Parameter $parameter): void
    {
        $user = auth()->user();
        if ($user) {
            if ($parameter->key === 'PROMPT' || $parameter->key === 'GEMINI_API_KEY') {
                $user->events()->create([
                    'type' => 'updated',
                    'description' => "Usuario {$user->username} ha actualizado el valor del parámetro {$parameter->key}."
                ]);
            } else {
                $original = $parameter->getOriginal()['value'];
                $changes = $parameter->getChanges()['value'];
                $user->events()->create([
                    'type' => 'updated',
                    'description' => "Usuario {$user->username} ha actualizado el valor del parámetro {$parameter->key} de '{$original}' a '{$changes}'."
                ]);
            }
        }
    }

    /**
     * Handle the Parameter "deleted" event.
     */
    public function deleted(Parameter $parameter): void
    {
        $user = auth()->user();
        $user->events()->create([
            'type' => 'deleted',
            'description' => "Usuario {$user->username} ha eliminado el parámetro {$parameter->key}:{$parameter->value} con ID:{$parameter->id}."
        ]);
    }
}
