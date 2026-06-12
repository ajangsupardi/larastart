<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

describe('Authentication', function () {
    describe('Login', function () {
        it('shows login form', function () {
            $this->get(route('login'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('auth/Login'));
        });

        it('can login with valid credentials', function () {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'password',
            ])->assertRedirect(route('dashboard'));

            $this->assertAuthenticatedAs($user);
        });

        it('fails with invalid credentials', function () {
            User::factory()->create([
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');

            $this->assertGuest();
        });

        it('validates required fields', function () {
            $this->post(route('login'), [])
                ->assertSessionHasErrors(['email', 'password']);
        });

        it('validates email format', function () {
            $this->post(route('login'), [
                'email' => 'not-an-email',
                'password' => 'password',
            ])->assertSessionHasErrors('email');
        });
    });

    describe('Register', function () {
        it('shows register form', function () {
            $this->get(route('register'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('auth/Register'));
        });

        it('can register a new user', function () {
            $this->post(route('register'), [
                'name' => 'Test User',
                'email' => 'new@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])->assertRedirect(route('dashboard'));

            $this->assertDatabaseHas('users', [
                'email' => 'new@example.com',
                'name' => 'Test User',
            ]);

            $this->assertAuthenticated();
        });

        it('validates required fields', function () {
            $this->post(route('register'), [])
                ->assertSessionHasErrors(['name', 'email', 'password']);
        });

        it('validates unique email', function () {
            User::factory()->create(['email' => 'taken@example.com']);

            $this->post(route('register'), [
                'name' => 'Test',
                'email' => 'taken@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])->assertSessionHasErrors('email');
        });

        it('validates password confirmation', function () {
            $this->post(route('register'), [
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'different-password',
            ])->assertSessionHasErrors('password');
        });

        it('validates password strength', function () {
            $this->post(route('register'), [
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])->assertSessionHasErrors('password');
        });
    });

    describe('Logout', function () {
        it('can logout', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $this->post(route('logout'))
                ->assertRedirect(route('home'));

            $this->assertGuest();
        });
    });

    describe('Forgot Password', function () {
        it('shows forgot password form', function () {
            $this->get(route('password.request'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('auth/ForgotPassword'));
        });

        it('sends reset link to valid email', function () {
            Notification::fake();

            $user = User::factory()->create(['email' => 'test@example.com']);

            $this->post(route('password.email'), [
                'email' => 'test@example.com',
            ])->assertRedirect();

            Notification::assertSentTo($user, ResetPasswordNotification::class);
        });

        it('validates required email', function () {
            $this->post(route('password.email'), [])
                ->assertSessionHasErrors('email');
        });

        it('validates email format', function () {
            $this->post(route('password.email'), [
                'email' => 'not-an-email',
            ])->assertSessionHasErrors('email');
        });
    });

    describe('Reset Password', function () {
        it('can reset password with valid token', function () {
            $user = User::factory()->create([
                'email' => 'test@example.com',
            ]);

            $token = Password::createToken($user);

            $this->post(route('password.store'), [
                'token' => $token,
                'email' => 'test@example.com',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])->assertRedirect(route('login'));

            $user->refresh();
            expect(Hash::check('NewPassword123!', $user->password))->toBeTrue();
        });

        it('validates required fields', function () {
            $this->post(route('password.store'), [])
                ->assertSessionHasErrors(['token', 'email', 'password']);
        });

        it('validates password confirmation', function () {
            $user = User::factory()->create(['email' => 'test@example.com']);
            $token = Password::createToken($user);

            $this->post(route('password.store'), [
                'token' => $token,
                'email' => 'test@example.com',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'different-password',
            ])->assertSessionHasErrors('password');
        });
    });

    describe('Email Verification', function () {
        it('shows verification notice for unverified user', function () {
            $user = User::factory()->unverified()->create();
            $this->actingAs($user);

            $this->get(route('verification.notice'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('auth/VerifyEmail'));
        });

        it('redirects verified user to dashboard', function () {
            $user = User::factory()->create(['email_verified_at' => now()]);
            $this->actingAs($user);

            $this->get(route('verification.notice'))
                ->assertRedirect(route('dashboard'));
        });

        it('sends verification notification', function () {
            Notification::fake();

            $user = User::factory()->unverified()->create();
            $this->actingAs($user);

            $this->post(route('verification.send'))
                ->assertRedirect();

            Notification::assertSentTo($user, VerifyEmail::class);
        });

        it('does not send notification to verified user', function () {
            Notification::fake();

            $user = User::factory()->create(['email_verified_at' => now()]);
            $this->actingAs($user);

            $this->post(route('verification.send'))
                ->assertRedirect(route('dashboard'));

            Notification::assertNothingSent();
        });
    });
});
