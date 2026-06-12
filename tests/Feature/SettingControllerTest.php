<?php

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Hash;

describe('SettingController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can view settings page', function () {
        $this->get(route('settings.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Settings/Index')
                ->has('settings')
                ->has('system'));
    });

    it('can update general settings', function () {
        $this->put(route('settings.general'), [
            'name' => 'My App',
            'timezone' => 'Asia/Jakarta',
        ])->assertRedirect(route('settings.index'));

        expect(Setting::get('app_name'))->toBe('My App');
        expect(Setting::get('app_timezone'))->toBe('Asia/Jakarta');
    });

    it('can update password', function () {
        $this->put(route('settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('settings.index'));

        $user = User::first();
        expect(Hash::check('new-password-123', $user->password))->toBeTrue();
    });

    it('validates current password when changing password', function () {
        $this->put(route('settings.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors(['current_password']);
    });

    it('can clear cache', function () {
        $this->post(route('settings.clear-cache'))
            ->assertRedirect();
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('settings.index'))->assertRedirect(route('login'));
        $this->put(route('settings.general'), [])->assertRedirect(route('login'));
        $this->put(route('settings.password'), [])->assertRedirect(route('login'));
        $this->post(route('settings.clear-cache'))->assertRedirect(route('login'));
    });

    it('requires users read permission for settings index', function () {
        $role = Role::factory()->create(['permissions' => []]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->get(route('settings.index'))
            ->assertForbidden();
    });

    it('requires users update permission for general settings', function () {
        $role = Role::factory()->create(['permissions' => ['users' => ['read']]]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->put(route('settings.general'), [
                'name' => 'Test',
                'timezone' => 'UTC',
            ])->assertForbidden();
    });

    it('requires users update permission for clear cache', function () {
        $role = Role::factory()->create(['permissions' => ['users' => ['read']]]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->post(route('settings.clear-cache'))
            ->assertForbidden();
    });
});
