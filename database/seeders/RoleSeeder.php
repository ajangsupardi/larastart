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
            'is_system' => true,
            'permissions' => [
                'users' => ['create', 'read', 'update', 'delete'],
                'roles' => ['create', 'read', 'update', 'delete'],
                'provinces' => ['create', 'read', 'update', 'delete'],
                'regencies' => ['create', 'read', 'update', 'delete'],
                'districts' => ['create', 'read', 'update', 'delete'],
                'villages' => ['create', 'read', 'update', 'delete'],
                'occupations' => ['create', 'read', 'update', 'delete'],
            ],
        ]);

        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Manage users and content.',
            'permissions' => [
                'users' => ['create', 'update', 'delete'],
                'roles' => ['create', 'read', 'update'],
                'provinces' => ['create', 'read', 'update', 'delete'],
                'regencies' => ['create', 'read', 'update', 'delete'],
                'districts' => ['create', 'read', 'update', 'delete'],
                'villages' => ['create', 'read', 'update', 'delete'],
                'occupations' => ['create', 'read', 'update', 'delete'],
            ],
        ]);

        Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'No access to admin resources.',
            'permissions' => [],
        ]);
    }
}
