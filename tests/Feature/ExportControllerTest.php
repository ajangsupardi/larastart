<?php

use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('ExportController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can export users as CSV', function () {
        User::factory(3)->create();

        $response = $this->get(route('export', 'users'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="users.csv"');
    });

    it('can export roles as CSV', function () {
        Role::factory(3)->create();

        $response = $this->get(route('export', 'roles'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="roles.csv"');
    });

    it('can export provinces as CSV', function () {
        Province::factory(3)->create();

        $response = $this->get(route('export', 'provinces'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="provinces.csv"');
    });

    it('returns 404 for invalid resource', function () {
        $this->get(route('export', 'invalid'))
            ->assertNotFound();
    });

    it('returns 403 without permission', function () {
        auth()->logout();

        $role = Role::factory()->create(['permissions' => []]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class)
            ->get(route('export', 'users'))
            ->assertForbidden();
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('export', 'users'))->assertRedirect(route('login'));
    });
});
