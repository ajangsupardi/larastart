<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Download kodepos data if not exists
        $csvPath = storage_path('app/kodepos/kodepos.csv');
        if (! file_exists($csvPath)) {
            $this->command->call('app:download-kodepos');
        }

        $this->call(RoleSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(RegencySeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(VillageSeeder::class);
        $this->call(OccupationSeeder::class);

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

        // Set created_by = 1 for all geographic data
        Province::query()->update(['created_by' => $superAdmin->id]);
        Regency::query()->update(['created_by' => $superAdmin->id]);
        District::query()->update(['created_by' => $superAdmin->id]);
        Village::query()->update(['created_by' => $superAdmin->id]);
        Occupation::query()->update(['created_by' => $superAdmin->id]);
    }
}
