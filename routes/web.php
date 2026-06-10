<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegencyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

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
        Route::get('search', SearchController::class)->name('search');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users,create');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users,create');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users,update');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users,update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users,delete');
        Route::post('users/{user}/avatar', [UserController::class, 'updateAvatar'])->name('users.avatar')->middleware('permission:users,update');

        // Roles
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles,create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles,create');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles,update');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles,update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles,delete');

        // Provinces
        Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index');
        Route::get('provinces/create', [ProvinceController::class, 'create'])->name('provinces.create')->middleware('permission:provinces,create');
        Route::post('provinces', [ProvinceController::class, 'store'])->name('provinces.store')->middleware('permission:provinces,create');
        Route::get('provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('provinces.edit')->middleware('permission:provinces,update');
        Route::put('provinces/{province}', [ProvinceController::class, 'update'])->name('provinces.update')->middleware('permission:provinces,update');
        Route::delete('provinces/{province}', [ProvinceController::class, 'destroy'])->name('provinces.destroy')->middleware('permission:provinces,delete');

        // Regencies
        Route::get('regencies', [RegencyController::class, 'index'])->name('regencies.index');
        Route::get('regencies/create', [RegencyController::class, 'create'])->name('regencies.create')->middleware('permission:regencies,create');
        Route::post('regencies', [RegencyController::class, 'store'])->name('regencies.store')->middleware('permission:regencies,create');
        Route::get('regencies/{regency}/edit', [RegencyController::class, 'edit'])->name('regencies.edit')->middleware('permission:regencies,update');
        Route::put('regencies/{regency}', [RegencyController::class, 'update'])->name('regencies.update')->middleware('permission:regencies,update');
        Route::delete('regencies/{regency}', [RegencyController::class, 'destroy'])->name('regencies.destroy')->middleware('permission:regencies,delete');

        // Districts
        Route::get('districts', [DistrictController::class, 'index'])->name('districts.index');
        Route::get('districts/create', [DistrictController::class, 'create'])->name('districts.create')->middleware('permission:districts,create');
        Route::post('districts', [DistrictController::class, 'store'])->name('districts.store')->middleware('permission:districts,create');
        Route::get('districts/{district}/edit', [DistrictController::class, 'edit'])->name('districts.edit')->middleware('permission:districts,update');
        Route::put('districts/{district}', [DistrictController::class, 'update'])->name('districts.update')->middleware('permission:districts,update');
        Route::delete('districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy')->middleware('permission:districts,delete');

        // Villages
        Route::get('villages', [VillageController::class, 'index'])->name('villages.index');
        Route::get('villages/create', [VillageController::class, 'create'])->name('villages.create')->middleware('permission:villages,create');
        Route::post('villages', [VillageController::class, 'store'])->name('villages.store')->middleware('permission:villages,create');
        Route::get('villages/{village}/edit', [VillageController::class, 'edit'])->name('villages.edit')->middleware('permission:villages,update');
        Route::put('villages/{village}', [VillageController::class, 'update'])->name('villages.update')->middleware('permission:villages,update');
        Route::delete('villages/{village}', [VillageController::class, 'destroy'])->name('villages.destroy')->middleware('permission:villages,delete');

        // Occupations
        Route::get('occupations', [OccupationController::class, 'index'])->name('occupations.index');
        Route::get('occupations/create', [OccupationController::class, 'create'])->name('occupations.create')->middleware('permission:occupations,create');
        Route::post('occupations', [OccupationController::class, 'store'])->name('occupations.store')->middleware('permission:occupations,create');
        Route::get('occupations/{occupation}/edit', [OccupationController::class, 'edit'])->name('occupations.edit')->middleware('permission:occupations,update');
        Route::put('occupations/{occupation}', [OccupationController::class, 'update'])->name('occupations.update')->middleware('permission:occupations,update');
        Route::delete('occupations/{occupation}', [OccupationController::class, 'destroy'])->name('occupations.destroy')->middleware('permission:occupations,delete');
    });
});
