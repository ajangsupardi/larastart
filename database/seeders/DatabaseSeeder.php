<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $editorRole = Role::where('slug', 'editor')->first();
        $viewerRole = Role::where('slug', 'viewer')->first();

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
        ]);
        $superAdmin->roles()->attach($superAdminRole);
        $superAdmin->update(['created_by' => $superAdmin->id]);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'created_by' => $superAdmin->id,
        ]);
        $adminUser->roles()->attach($adminRole);

        $editorUser = User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'created_by' => $superAdmin->id,
        ]);
        $editorUser->roles()->attach($editorRole);

        User::factory(5)->create(['created_by' => $superAdmin->id])
            ->each(fn ($user) => $user->roles()->attach($viewerRole));
    }
}
