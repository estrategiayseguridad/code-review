<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new class extends Component
{
    public $roles;

    public function mount(): void
    {
        $this->roles = Role::all();
    }

    public function edit($roleId): void
    {
        $this->redirectRoute('roles.edit', ['roleId' => $roleId]);
    }

    public function delete($roleId): void
    {
        Role::find($roleId)->delete();
        $this->roles = Role::all();
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8">

            @can('create role')
            <div class="d-grid mb-4">
                <a class="btn btn-sm btn-light" href="{{ route('roles.new') }}" wire:navigate>
                    <i class="fa-solid fa-plus pe-2"></i>{{ __('New') }}
                </a>
            </div>
            @endcan

            <div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            @foreach ($roles as $role)
                            <tr wire:key="{{ $role->id }}">
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $role->name }}</td>
                                <td class="fit">
                                    @can('update role')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-light me-2"
                                            wire:click="edit({{ $role->id }})">
                                                <i class="fa-solid fa-pen"></i>
                                        </button>
                                    @endcan
                                    @can('delete role')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-light"
                                            wire:click="delete({{ $role->id }})"
                                            wire:confirm="¿Desea continuar con la eliminación?">
                                                <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
