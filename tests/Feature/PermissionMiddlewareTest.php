<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('Permission Middleware on Index Routes', function () {
    beforeEach(function () {
        $this->withoutMiddleware(PreventRequestForgery::class);
    });

    it('blocks access to provinces index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('provinces.index'))
            ->assertForbidden();
    });

    it('allows access to provinces index with read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['provinces' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('provinces.index'))
            ->assertOk();
    });

    it('blocks access to regencies index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('regencies.index'))
            ->assertForbidden();
    });

    it('blocks access to districts index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('districts.index'))
            ->assertForbidden();
    });

    it('blocks access to villages index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('villages.index'))
            ->assertForbidden();
    });

    it('blocks access to occupations index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('occupations.index'))
            ->assertForbidden();
    });

    it('blocks access to users index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['provinces' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    });

    it('blocks access to roles index without read permission', function () {
        $role = Role::factory()->create([
            'permissions' => ['users' => ['read']],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertForbidden();
    });

    it('blocks access to all index routes when user has no permissions at all', function () {
        $role = Role::factory()->create([
            'permissions' => [],
        ]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $routes = [
            'users.index',
            'roles.index',
            'provinces.index',
            'regencies.index',
            'districts.index',
            'villages.index',
            'occupations.index',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertForbidden();
        }
    });
});
