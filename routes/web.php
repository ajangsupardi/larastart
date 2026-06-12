<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegencyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:3,1');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->middleware('throttle:3,1')->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('email/verify', [EmailVerificationController::class, 'create'])->name('verification.notice');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::middleware('verified')->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('search', SearchController::class)->middleware('throttle:30,1')->name('search');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users,read');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users,create');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users,create');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users,update');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware(['permission:users,update', 'ownership:users,update']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware(['permission:users,delete', 'ownership:users,delete']);
        Route::post('users/{user}/avatar', [UserController::class, 'updateAvatar'])->name('users.avatar')->middleware('permission:users,update');

        // Roles
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles,read');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles,create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles,create');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles,update');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware(['permission:roles,update', 'ownership:roles,update']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware(['permission:roles,delete', 'ownership:roles,delete']);

        // Provinces
        Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index')->middleware('permission:provinces,read');
        Route::get('provinces/create', [ProvinceController::class, 'create'])->name('provinces.create')->middleware('permission:provinces,create');
        Route::post('provinces', [ProvinceController::class, 'store'])->name('provinces.store')->middleware('permission:provinces,create');
        Route::get('provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('provinces.edit')->middleware('permission:provinces,update');
        Route::put('provinces/{province}', [ProvinceController::class, 'update'])->name('provinces.update')->middleware(['permission:provinces,update', 'ownership:provinces,update']);
        Route::delete('provinces/{province}', [ProvinceController::class, 'destroy'])->name('provinces.destroy')->middleware(['permission:provinces,delete', 'ownership:provinces,delete']);

        // Regencies
        Route::get('regencies', [RegencyController::class, 'index'])->name('regencies.index')->middleware('permission:regencies,read');
        Route::get('regencies/create', [RegencyController::class, 'create'])->name('regencies.create')->middleware('permission:regencies,create');
        Route::post('regencies', [RegencyController::class, 'store'])->name('regencies.store')->middleware('permission:regencies,create');
        Route::get('regencies/{regency}/edit', [RegencyController::class, 'edit'])->name('regencies.edit')->middleware('permission:regencies,update');
        Route::put('regencies/{regency}', [RegencyController::class, 'update'])->name('regencies.update')->middleware(['permission:regencies,update', 'ownership:regencies,update']);
        Route::delete('regencies/{regency}', [RegencyController::class, 'destroy'])->name('regencies.destroy')->middleware(['permission:regencies,delete', 'ownership:regencies,delete']);

        // Districts
        Route::get('districts', [DistrictController::class, 'index'])->name('districts.index')->middleware('permission:districts,read');
        Route::get('districts/create', [DistrictController::class, 'create'])->name('districts.create')->middleware('permission:districts,create');
        Route::post('districts', [DistrictController::class, 'store'])->name('districts.store')->middleware('permission:districts,create');
        Route::get('districts/{district}/edit', [DistrictController::class, 'edit'])->name('districts.edit')->middleware('permission:districts,update');
        Route::put('districts/{district}', [DistrictController::class, 'update'])->name('districts.update')->middleware(['permission:districts,update', 'ownership:districts,update']);
        Route::delete('districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy')->middleware(['permission:districts,delete', 'ownership:districts,delete']);

        // Villages
        Route::get('villages', [VillageController::class, 'index'])->name('villages.index')->middleware('permission:villages,read');
        Route::get('villages/create', [VillageController::class, 'create'])->name('villages.create')->middleware('permission:villages,create');
        Route::post('villages', [VillageController::class, 'store'])->name('villages.store')->middleware('permission:villages,create');
        Route::get('villages/{village}/edit', [VillageController::class, 'edit'])->name('villages.edit')->middleware('permission:villages,update');
        Route::put('villages/{village}', [VillageController::class, 'update'])->name('villages.update')->middleware(['permission:villages,update', 'ownership:villages,update']);
        Route::delete('villages/{village}', [VillageController::class, 'destroy'])->name('villages.destroy')->middleware(['permission:villages,delete', 'ownership:villages,delete']);

        // Export
        Route::get('export/{resource}', [ExportController::class, 'export'])->name('export');

        // Occupations
        Route::get('occupations', [OccupationController::class, 'index'])->name('occupations.index')->middleware('permission:occupations,read');
        Route::get('occupations/create', [OccupationController::class, 'create'])->name('occupations.create')->middleware('permission:occupations,create');
        Route::post('occupations', [OccupationController::class, 'store'])->name('occupations.store')->middleware('permission:occupations,create');
        Route::get('occupations/{occupation}/edit', [OccupationController::class, 'edit'])->name('occupations.edit')->middleware('permission:occupations,update');
        Route::put('occupations/{occupation}', [OccupationController::class, 'update'])->name('occupations.update')->middleware(['permission:occupations,update', 'ownership:occupations,update']);
        Route::delete('occupations/{occupation}', [OccupationController::class, 'destroy'])->name('occupations.destroy')->middleware(['permission:occupations,delete', 'ownership:occupations,delete']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

        // Trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('users', [TrashController::class, 'users'])->name('users')->middleware('permission:users,read');
            Route::post('users/{user}/restore', [TrashController::class, 'restoreUser'])->name('users.restore')->middleware('permission:users,update')->withTrashed();
            Route::delete('users/{user}', [TrashController::class, 'forceDeleteUser'])->name('users.force-delete')->middleware('permission:users,delete')->withTrashed();

            Route::get('roles', [TrashController::class, 'roles'])->name('roles')->middleware('permission:roles,read');
            Route::post('roles/{role}/restore', [TrashController::class, 'restoreRole'])->name('roles.restore')->middleware('permission:roles,update')->withTrashed();
            Route::delete('roles/{role}', [TrashController::class, 'forceDeleteRole'])->name('roles.force-delete')->middleware('permission:roles,delete')->withTrashed();
        });

        // Audit Log
        Route::get('audit-log', [AuditController::class, 'index'])->name('audit-log')->middleware('permission:users,read');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:users,read');
        Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general')->middleware('permission:users,update');
        Route::put('settings/appearance', [SettingController::class, 'updateAppearance'])->name('settings.appearance');
        Route::put('settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
        Route::put('settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications');
        Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache')->middleware('permission:users,update');
        Route::get('settings/system-info', [SettingController::class, 'systemInfo'])->name('settings.system-info')->middleware('permission:users,read');
    });
});
