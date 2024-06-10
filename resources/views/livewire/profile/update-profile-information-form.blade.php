<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $username = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->first_name = Auth::user()->first_name;
        $this->last_name = Auth::user()->last_name;
        $this->username = Auth::user()->username;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'first_name' => ['max:255'],
            'last_name' => ['max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $validated['first_name'] = empty($validated['first_name']) ? null : $validated['first_name'];
        $validated['last_name'] = empty($validated['last_name']) ? null : $validated['last_name'];

        $user->fill($validated);

        $user->save();

        $this->dispatch('profile-updated', name: $user->username);
    }

}; ?>

<div>
    <header>
        <h2>Información Personal</h2>
        <p class="mt-1">Actualice la información del perfil de su cuenta.</p>
    </header>

    <div class="card mb-4">
        <div class="card-body">
            <form wire:submit="updateProfileInformation" class="d-grid gap-4">
                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input wire:model="username" id="username" name="username" type="text" class="mt-1 w-100" required autofocus autocomplete="username" />
                    <x-input-error class="mt-2" :for="'username'" />
                </div>
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" />
                    <x-text-input wire:model="first_name" id="first_name" name="first_name" type="text" class="mt-1 w-100" autocomplete="first_name" />
                    <x-input-error class="mt-2" :for="'first_name'" />
                </div>
                <div>
                    <x-input-label for="last_name" :value="__('Last Name')" />
                    <x-text-input wire:model="last_name" id="last_name" name="last_name" type="text" class="mt-1 w-100" autocomplete="last_name" />
                    <x-input-error class="mt-2" :for="'last_name'" />
                </div>

                <div class="d-flex align-items-center gap-4">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                    <x-action-message on="profile-updated">{{ __('Saved.') }}</x-action-message>
                </div>
            </form>
        </div>
    </div>
</div>
