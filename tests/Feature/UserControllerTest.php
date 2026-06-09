<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Hash;

describe('UserController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list users', function () {
        User::factory(3)->create();

        $this->get(route('users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Users/Index')
                ->has('users.data', 4)
                ->has('filters'));
    });

    it('can search users', function () {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $this->get(route('users.index', ['search' => 'John']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Users/Index')
                ->has('users.data', 1));
    });

    it('shows create form', function () {
        $this->get(route('users.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Users/Create'));
    });

    it('can create a user', function () {
        $this->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'name' => 'New User',
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('users.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    });

    it('validates unique email when creating', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('users.store'), [
            'name' => 'Test',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['email']);
    });

    it('shows edit form', function () {
        $user = User::factory()->create();

        $this->get(route('users.edit', $user))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Users/Edit')
                ->has('user'));
    });

    it('can update a user', function () {
        $user = User::factory()->create();

        $this->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    });

    it('can update password', function () {
        $user = User::factory()->create();

        $this->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('users.index'));

        expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
    });

    it('validates unique email when updating', function () {
        $other = User::factory()->create(['email' => 'other@example.com']);
        $user = User::factory()->create();

        $this->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => 'other@example.com',
        ])->assertSessionHasErrors(['email']);
    });

    it('allows same email when updating own record', function () {
        $user = User::factory()->create();

        $this->put(route('users.update', $user), [
            'name' => 'Updated',
            'email' => $user->email,
        ])->assertRedirect(route('users.index'));
    });

    it('can delete a user', function () {
        $user = User::factory()->create();

        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('users.index'))->assertRedirect(route('login'));
        $this->get(route('users.create'))->assertRedirect(route('login'));
        $this->post(route('users.store'), [])->assertRedirect(route('login'));
        $this->get(route('users.edit', 1))->assertRedirect(route('login'));
        $this->put(route('users.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('users.destroy', 1))->assertRedirect(route('login'));
    });
});
