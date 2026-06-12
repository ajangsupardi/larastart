<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('TrashController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list trashed users', function () {
        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $this->get(route('trash.users'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Trash/Users')
                ->has('users.data', 1)
                ->has('filters'));
    });

    it('can list trashed roles', function () {
        $deletedRole = Role::factory()->create();
        $deletedRole->delete();

        $this->get(route('trash.roles'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Trash/Roles')
                ->has('roles.data', 1)
                ->has('filters'));
    });

    it('can restore a trashed user', function () {
        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $this->post(route('trash.users.restore', $deletedUser))
            ->assertRedirect(route('trash.users'));

        expect($deletedUser->fresh()->trashed())->toBeFalse();
    });

    it('can restore a trashed role', function () {
        $deletedRole = Role::factory()->create();
        $deletedRole->delete();

        $this->post(route('trash.roles.restore', $deletedRole))
            ->assertRedirect(route('trash.roles'));

        expect($deletedRole->fresh()->trashed())->toBeFalse();
    });

    it('can force delete a trashed user', function () {
        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $this->delete(route('trash.users.force-delete', $deletedUser))
            ->assertRedirect(route('trash.users'));

        $this->assertDatabaseMissing('users', ['id' => $deletedUser->id]);
    });

    it('can force delete a trashed role', function () {
        $deletedRole = Role::factory()->create();
        $deletedRole->delete();

        $this->delete(route('trash.roles.force-delete', $deletedRole))
            ->assertRedirect(route('trash.roles'));

        $this->assertDatabaseMissing('roles', ['id' => $deletedRole->id]);
    });

    it('requires authentication for all trash routes', function () {
        auth()->logout();

        $this->get(route('trash.users'))->assertRedirect(route('login'));
        $this->get(route('trash.roles'))->assertRedirect(route('login'));
        $this->post(route('trash.users.restore', 1))->assertRedirect(route('login'));
        $this->post(route('trash.roles.restore', 1))->assertRedirect(route('login'));
        $this->delete(route('trash.users.force-delete', 1))->assertRedirect(route('login'));
        $this->delete(route('trash.roles.force-delete', 1))->assertRedirect(route('login'));
    });

    it('requires users read permission for trash.users', function () {
        auth()->logout();

        $role = Role::factory()->create(['permissions' => []]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->get(route('trash.users'))
            ->assertForbidden();
    });

    it('requires users update permission for restore user', function () {
        $role = Role::factory()->create(['permissions' => ['users' => ['read']]]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->post(route('trash.users.restore', $deletedUser))
            ->assertForbidden();
    });

    it('requires users delete permission for force delete user', function () {
        $role = Role::factory()->create(['permissions' => ['users' => ['read', 'update']]]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->delete(route('trash.users.force-delete', $deletedUser))
            ->assertForbidden();
    });
});
