<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('RoleController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list roles', function () {
        Role::factory(2)->create();

        $this->get(route('roles.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Roles/Index')
                ->has('roles.data', 3)
                ->has('filters'));
    });

    it('can search roles', function () {
        Role::factory()->create(['name' => 'UniqueAdminRole']);
        Role::factory()->create(['name' => 'Content Writer']);

        $this->get(route('roles.index', ['search' => 'UniqueAdmin']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Roles/Index')
                ->has('roles.data', 1));
    });

    it('shows create form', function () {
        $this->get(route('roles.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Roles/Create')
                ->has('resources')
                ->has('actions'));
    });

    it('can create a role', function () {
        $this->post(route('roles.store'), [
            'name' => 'Administrator',
            'slug' => 'administrator',
            'description' => 'Full access role',
            'permissions' => [
                'users' => ['create', 'update', 'delete'],
                'roles' => ['create', 'update', 'delete'],
            ],
        ])->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', [
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('roles.store'), [])
            ->assertSessionHasErrors(['name', 'slug', 'permissions']);
    });

    it('validates unique slug when creating', function () {
        $existing = Role::factory()->create();

        $this->post(route('roles.store'), [
            'name' => 'Admin',
            'slug' => $existing->slug,
            'permissions' => ['users' => ['create']],
        ])->assertSessionHasErrors(['slug']);
    });

    it('shows edit form', function () {
        $role = Role::factory()->create();

        $this->get(route('roles.edit', $role))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Roles/Edit')
                ->has('role')
                ->has('resources')
                ->has('actions'));
    });

    it('can update a role', function () {
        $role = Role::factory()->create();

        $this->put(route('roles.update', $role), [
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Updated description',
            'permissions' => [
                'users' => ['create', 'update'],
            ],
        ])->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);
    });

    it('validates unique slug when updating', function () {
        $role = Role::factory()->create(['slug' => 'my-role']);
        $other = Role::factory()->create(['slug' => 'editor-role']);

        $this->put(route('roles.update', $role), [
            'name' => 'Updated',
            'slug' => 'editor-role',
            'permissions' => ['users' => ['create']],
        ])->assertSessionHasErrors(['slug']);
    });

    it('allows same slug when updating own record', function () {
        $role = Role::factory()->create(['slug' => 'my-role']);

        $this->put(route('roles.update', $role), [
            'name' => 'Updated',
            'slug' => 'my-role',
            'permissions' => ['users' => ['create']],
        ])->assertRedirect(route('roles.index'));
    });

    it('can delete a role', function () {
        $role = Role::factory()->create();

        $this->delete(route('roles.destroy', $role))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('roles.index'))->assertRedirect(route('login'));
        $this->get(route('roles.create'))->assertRedirect(route('login'));
        $this->post(route('roles.store'), [])->assertRedirect(route('login'));
        $this->get(route('roles.edit', 1))->assertRedirect(route('login'));
        $this->put(route('roles.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('roles.destroy', 1))->assertRedirect(route('login'));
    });
});
