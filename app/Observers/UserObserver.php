<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $creator = auth()->user();
        if ($creator) {
            $creator->events()->create([
                'type' => 'created',
                'description' => "Usuario {$creator->username} ha creado el usuario {$user->username} con ID:{$user->id}."
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $creator = auth()->user();
        if ($creator) {
            $creator->events()->create([
                'type' => 'deleted',
                'description' => "Usuario {$creator->username} ha eliminado el usuario {$user->username} con ID:{$user->id}."
            ]);
        }
    }
}
