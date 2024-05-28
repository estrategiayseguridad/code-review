<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

new class extends Component
{
    public Role $role;
    public $permissions, $rolePermissions;
    public $checkedPermissions = [];

    public $name = '';

    public function mount($roleId): void
    {
        $this->permissions = Permission::all();
        $this->role = Role::find($roleId);
        if ($this->role) {
            $this->name = $this->role->name;
            $this->rolePermissions = DB::table('role_has_permissions')
                    ->where('role_has_permissions.role_id', $this->role->id)
                    ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                    ->all();
            $this->checkedPermissions = DB::table('role_has_permissions')
                    ->where('role_has_permissions.role_id', $this->role->id)
                    ->pluck('role_has_permissions.permission_id')
                    ->all();
            dd($this->checkedPermissions);
        } else {
            $this->redirectRoute(url()->previous());
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles')->ignore($this->role),
            ]
        ]);
        $this->role->name = $this->name;
        $this->role->save();
        $this->redirectRoute('roles.index');
    }

    public function savePermissions(): void
    {
        $this->validate([
            'checkedPermissions' => 'required'
        ]);
        $this->role->syncPermissions($this->checkedPermissions);
    }

}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8">

            {{-- ROLE FORM --}}
            <div class="card">
                <div class="card-header">{{ __('Role') }}</div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row gy-3">
                            <div class="col-12 col-md">
                                <x-text-input type="text" wire:model="name" name="name" />
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-auto">
                                <x-primary-button type="submit" wire:loading.remove wire:target="save">{{ __('Save') }}</x-primary-button>
                                <x-primary-button wire:loading wire:target="save">
                                    <span role="status">{{ __('Saving') . '...' }}</span>
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PERMISSIONS FORM --}}
            <div class="card mt-4">
                <div class="card-header">{{ __('Permissions') }}</div>
                <div class="card-body">
                    <form wire:submit="savePermissions">
                        <div class="row">
                            @foreach ($permissions as $permission)
                            <div class="col-12 col-md-3">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        wire:model="checkedPermissions"
                                        name="checkedPermissions[]"
                                        class="form-check-input"
                                        value="{{ $permission->name }}"
                                        {{ in_array($permission->id, $rolePermissions) ? 'checked':'' }}>
                                    <label class="form-check-label">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                            <div class="col-12 mt-3">
                                <x-primary-button type="submit" wire:loading.remove wire:target="savePermissions">{{ __('Save') }}</x-primary-button>
                                <x-primary-button wire:loading wire:target="savePermissions">
                                    <span role="status">{{ __('Saving') . '...' }}</span>
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
