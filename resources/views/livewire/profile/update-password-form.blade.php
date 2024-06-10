<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<div>
    <header>
        <h2>{{ __('Update :name', ['name' => __('Password')]) }}</h2>
        <p class="mt-1">Asegúrese de que su cuenta utilice una contraseña larga y aleatoria para mantenerse segura.</p>
    </header>

    <div class="card mb-4">
        <div class="card-body">
            <form wire:submit="updatePassword" class="d-grid gap-4">
                <div>
                    <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                    <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 w-100" autocomplete="current-password" />
                    <x-input-error :for="'current_password'" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password" :value="__('New :name', ['name' => __('Password')])" />
                    <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 w-100" autocomplete="new-password" />
                    <x-input-error :for="'password'" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password_confirmation" :value="__('Confirm') . ' ' . __('Password')" />
                    <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 w-100" autocomplete="new-password" />
                    <x-input-error :for="'password_confirmation'" class="mt-2" />
                </div>

                <div class="d-flex align-items-center gap-4">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                    <x-action-message on="password-updated">{{ __('Saved.') }}</x-action-message>
                </div>
            </form>
        </div>
    </div>
</div>
