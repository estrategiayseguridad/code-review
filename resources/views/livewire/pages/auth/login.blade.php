<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-6 col-xl-4">
            <div class="m-3 p-3 text-center">
                <x-application-logo />
            </div>
            <div class="card">
                <div class="card-body">

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form wire:submit="login" class="d-grid gap-3">

                        <!-- Username Address -->
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input
                                type="text"
                                wire:model="form.username"
                                id="username"
                                name="username"
                                autocomplete="username"
                                autofocus
                                required />
                            <x-input-error :for="'form.username'" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input
                                type="password"
                                wire:model="form.password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required />
                            <x-input-error :for="'form.password'" />
                        </div>

                        <!-- Remember Me -->
                        <div>
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    wire:model="form.remember"
                                    id="remember"
                                    name="remember"
                                    class="form-check-input">
                                <label for="remember" class="form-check-label">{{ __('Remember Me') }}</label>
                            </div>
                        </div>

                        <x-primary-button>{{ __('Login') }}</x-primary-button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
