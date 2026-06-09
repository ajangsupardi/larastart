<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Full access to all resources.',
            'permissions' => [
                'users' => ['create', 'read', 'update', 'delete'],
                'roles' => ['create', 'read', 'update', 'delete'],
            ],
        ]);

        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Manage users and content.',
            'permissions' => [
                'users' => ['create', 'update', 'delete'],
                'roles' => ['create', 'read', 'update'],
            ],
        ]);

        Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Can manage users.',
            'permissions' => [
                'users' => ['create', 'read', 'update'],
                'roles' => ['read'],
            ],
        ]);

        Role::create([
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only access.',
            'permissions' => [
                'users' => ['read'],
                'roles' => ['read'],
            ],
        ]);
    }
}
