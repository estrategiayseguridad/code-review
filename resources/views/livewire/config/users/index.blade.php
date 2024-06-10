<?php

use App\Models\Parameter;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    #[Validate('required|string|min:4|max:255|unique:users,username', as: 'Nombre de Usuario')]
    public $username;

    #[Validate('max:255', as: 'Nombres')]
    public $first_name;

    #[Validate('max:255', as: 'Apellidos')]
    public $last_name;

    #[Validate('required|email|lowercase|min:4|max:255', as: 'Correo Electrónico')]
    public $email;

    public $password;

    public $password_confirmation;

    public $eye1 = false, $eye2 = false;

    public function resetData(): void
    {
        $this->username = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->email = null;
        $this->password = null;
        $this->password_confirmation = null;
        $this->eye1 = false;
        $this->eye2 = false;
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('livewire.config.users.index', [
            'pagination' => User::where('id', '>', 1)->orderByDesc('created_at')->paginate(Parameter::val('PAGINATION_ROWS_PER_PAGE'))
        ]);
    }

    public function mount(): void
    {

    }

    public function register(): void
    {
        $validated = $this->validate([
            'first_name'    => ['max:255'],
            'last_name'     => ['max:255'],
            'username'      => ['required', 'string', 'min:6', 'max:255', 'unique:' . User::class],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['first_name'] = empty($validated['first_name']) ? null : $validated['first_name'];
        $validated['last_name'] = empty($validated['last_name']) ? null : $validated['last_name'];
        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        if ($user) {
            $this->dispatch('user-saved');
            $this->resetData();
        }
    }

    public function generatePassword(): void
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
        $this->password = substr(str_shuffle($chars), 0, 12);
    }

    public function setAdminRole($is_admin, $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->update(['is_admin' => $is_admin])) {
            $this->dispatch('user-updated');
        }
    }

    public function delete($userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->delete()) {
            $this->resetData();
        }
    }

}; ?>

@section('header')
    <livewire:layout.header-config />
@endsection

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <x-card>
            <x-slot:heading>
                {{ __('New :name', ['name' => __('User')]) }}
                <div class="text-lowercase float-end">
                    <x-action-message on="user-saved">{{ __('Saved.') }}</x-action-message>
                </div>
            </x-slot>
            <x-slot:body>

                {{-- Create Form --}}
                <form wire:submit="register" class="d-grid gap-3">

                    <div class="row">
                        <x-input-label :value="__('Username')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <x-text-input type="text" wire:model="username" required />
                            <x-input-error :for="'username'" />
                        </div>
                    </div>

                    <div class="row">
                        <x-input-label :value="__('First Name')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <x-text-input type="text" wire:model="first_name" />
                            <x-input-error :for="'first_name'" />
                        </div>
                    </div>

                    <div class="row">
                        <x-input-label :value="__('Last Name')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <x-text-input type="text" wire:model="last_name" />
                            <x-input-error :for="'last_name'" />
                        </div>
                    </div>

                    <div class="row">
                        <x-input-label :value="__('Email')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <x-text-input type="email" wire:model="email" required />
                            <x-input-error :for="'email'" />
                        </div>
                    </div>

                    <div class="row">
                        <x-input-label :value="__('Password')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <div class="input-group">
                                <x-text-input type="{{ $eye1 ? 'text' : 'password' }}" wire:model="password" />
                                <button type="button" class="btn btn-outline-secondary" wire:click="generatePassword" title="Generar aleatoriamente">
                                    <i class="fa-solid fa-shuffle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" wire:click="$toggle('eye1')">
                                    <i class="fa-regular {{ $eye1 ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                            </div>
                            <x-input-error :for="'password'" />
                        </div>
                    </div>

                    <div class="row">
                        <x-input-label :value="__('Confirm')" class="col-sm-3 col-form-label text-end" />
                        <div class="col-sm-9">
                            <div class="input-group">
                                <x-text-input type="{{ $eye2 ? 'text' : 'password' }}" wire:model="password_confirmation" />
                                <button type="button" class="btn btn-outline-secondary" wire:click="$toggle('eye2')">
                                    <i class="fa-regular {{ $eye2 ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                            </div>
                            <x-input-error :for="'password_confirmation'" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="offset-sm-3 col-sm-9">
                            <x-primary-button wire:loading.remove wire:target='register'>{{ __('Save') }}</x-primary-button>
                            <x-primary-button wire:loading wire:target='register'>
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card>

        <x-card class="mt-4">
            <x-slot:heading>
                {{ __('Users') }}
                <div class="text-lowercase float-end">
                    <x-action-message on="user-updated">{{ __('Saved.') }}</x-action-message>
                </div>
            </x-slot>
            <x-slot:body>

                {{-- List --}}
                <x-table class="table-striped">
                    <x-slot:body>
                        @forelse($pagination->items() as $user)
                        <tr wire:key="{{ $user->id }}">
                            <th scope="row" class="fit">
                                {{ $pagination->firstItem() + $loop->index }}
                            </th>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="fit text-end">
                                @if ($user->is_admin)
                                <button
                                    type="button"
                                    class="btn btn-sm btn-light me-1"
                                    wire:click="setAdminRole(false, {{ $user->id }})">
                                        <i class="fa-solid fa-user-tie pe-1"></i>{{ __('Administrator') }}
                                </button>
                                @else
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light me-1"
                                    wire:click="setAdminRole(true, {{ $user->id }})">
                                        <i class="fa-solid fa-user-astronaut pe-1"></i>{{ __('Pentester') }}
                                </button>
                                @endif
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-light"
                                    wire:click="delete({{ $user->id }})"
                                    wire:confirm="¿Desea continuar con la eliminación?">
                                        <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">{{ __('No Content') }}</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-table>

                {{ $pagination->links(data: ['scrollTo' => false]) }}

            </x-slot>
        </x-card>

    </div>
</div>
