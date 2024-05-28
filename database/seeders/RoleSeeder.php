<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $pentesterRole = Role::create(['name' => 'pentester']);

        // Give all permission to admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();

        $adminRole->givePermissionTo($allPermissionNames);

        // Give few permissions to admin role.
        $pentesterRole->givePermissionTo([
            'create project', 'read project', 'update project',
            'read file', 'update file',
            'read vulnerability', 'update vulnerability',
            'create job', 'read job', 'update job', 'delete job',
            'create language', 'read language', 'update language', 'delete language',
            'create extension', 'read extension', 'update extension', 'delete extension',
        ]);

        $adminUser = User::find(1);
        $adminUser->assignRole($adminRole);

        $pentesterUser = User::firstOrCreate([
            'email' => 'pentester@gmail.com',
        ], [
            'username' => 'pentester',
            'email' => 'pentester@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $pentesterUser->assignRole($pentesterRole);

    }
}
