<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create analysis']);
        Permission::create(['name' => 'read analysis']);
        Permission::create(['name' => 'update analysis']);
        Permission::create(['name' => 'delete analysis']);

        Permission::create(['name' => 'create event']);
        Permission::create(['name' => 'read event']);
        Permission::create(['name' => 'update event']);
        Permission::create(['name' => 'delete event']);

        Permission::create(['name' => 'create extension']);
        Permission::create(['name' => 'read extension']);
        Permission::create(['name' => 'update extension']);
        Permission::create(['name' => 'delete extension']);

        Permission::create(['name' => 'create file']);
        Permission::create(['name' => 'read file']);
        Permission::create(['name' => 'update file']);
        Permission::create(['name' => 'delete file']);

        Permission::create(['name' => 'create job']);
        Permission::create(['name' => 'read job']);
        Permission::create(['name' => 'update job']);
        Permission::create(['name' => 'delete job']);

        Permission::create(['name' => 'create language']);
        Permission::create(['name' => 'read language']);
        Permission::create(['name' => 'update language']);
        Permission::create(['name' => 'delete language']);

        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'read permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'create project']);
        Permission::create(['name' => 'read project']);
        Permission::create(['name' => 'update project']);
        Permission::create(['name' => 'delete project']);

        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'read role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'read user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'create vulnerability']);
        Permission::create(['name' => 'read vulnerability']);
        Permission::create(['name' => 'update vulnerability']);
        Permission::create(['name' => 'delete vulnerability']);
    }
}
