<?php

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('NotificationController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list notifications (audit logs)', function () {
        $user = User::first();
        for ($i = 0; $i < 3; $i++) {
            AuditLog::create([
                'user_id' => $user->id,
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'action' => 'created',
            ]);
        }

        $response = $this->getJson(route('notifications.index'));

        $response->assertOk()
            ->assertJsonCount(3);
    });

    it('can get unread count', function () {
        $user = User::first();
        for ($i = 0; $i < 5; $i++) {
            AuditLog::create([
                'user_id' => $user->id,
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'action' => 'created',
                'created_at' => now(),
            ]);
        }

        $response = $this->getJson(route('notifications.unread'));

        $response->assertOk()
            ->assertJsonPath('count', 5);
    });

    it('returns zero unread count when no recent audit logs', function () {
        // Clear any audit logs created by observers during setup
        AuditLog::query()->delete();

        $user = User::first();
        for ($i = 0; $i < 3; $i++) {
            $log = AuditLog::create([
                'user_id' => $user->id,
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'action' => 'created',
            ]);
            $log->forceFill(['created_at' => now()->subDays(2)])->save();
        }

        $response = $this->getJson(route('notifications.unread'));

        $response->assertOk()
            ->assertJsonPath('count', 0);
    });

    it('can mark notifications as read', function () {
        $response = $this->postJson(route('notifications.mark-read'));

        $response->assertOk()
            ->assertJsonPath('success', true);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->getJson(route('notifications.index'))->assertRedirect(route('login'));
        $this->getJson(route('notifications.unread'))->assertRedirect(route('login'));
        $this->postJson(route('notifications.mark-read'))->assertRedirect(route('login'));
    });
});
