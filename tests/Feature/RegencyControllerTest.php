<?php

use App\Models\Province;
use App\Models\Regency;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('RegencyController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list regencies', function () {
        Regency::factory(3)->create();

        $this->get(route('regencies.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Regencies/Index')
                ->has('regencies.data', 3)
                ->has('filters')
                ->has('provinces')
                ->has('stats'));
    });

    it('can search regencies by name', function () {
        Regency::factory()->create(['name' => 'Kota Bandung']);
        Regency::factory()->create(['name' => 'Kota Jakarta']);

        $this->get(route('regencies.index', ['search' => 'Bandung']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Regencies/Index')
                ->has('regencies.data', 1));
    });

    it('can filter regencies by province', function () {
        $province = Province::factory()->create();
        Regency::factory()->create(['province_id' => $province->id]);
        Regency::factory()->create();

        $this->get(route('regencies.index', ['province_id' => $province->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Regencies/Index')
                ->has('regencies.data', 1));
    });

    it('shows create form with provinces', function () {
        $this->get(route('regencies.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Regencies/Create')
                ->has('provinces'));
    });

    it('can create a regency', function () {
        $province = Province::factory()->create();

        $this->post(route('regencies.store'), [
            'name' => 'Kota Bandung',
            'province_id' => $province->id,
        ])->assertRedirect(route('regencies.index'));

        $this->assertDatabaseHas('regencies', [
            'name' => 'Kota Bandung',
            'province_id' => $province->id,
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('regencies.store'), [])
            ->assertSessionHasErrors(['name', 'province_id']);
    });

    it('validates province exists when creating', function () {
        $this->post(route('regencies.store'), [
            'name' => 'Test',
            'province_id' => 9999,
        ])->assertSessionHasErrors('province_id');
    });

    it('shows edit form with regency data', function () {
        $regency = Regency::factory()->create();

        $this->get(route('regencies.edit', $regency))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Regencies/Edit')
                ->has('regency')
                ->has('provinces'));
    });

    it('can update a regency', function () {
        $regency = Regency::factory()->create();
        $newProvince = Province::factory()->create();

        $this->put(route('regencies.update', $regency), [
            'name' => 'Updated Regency',
            'province_id' => $newProvince->id,
        ])->assertRedirect(route('regencies.index'));

        $this->assertDatabaseHas('regencies', [
            'id' => $regency->id,
            'name' => 'Updated Regency',
            'province_id' => $newProvince->id,
        ]);
    });

    it('can delete a regency', function () {
        $regency = Regency::factory()->create();

        $this->delete(route('regencies.destroy', $regency))
            ->assertRedirect(route('regencies.index'));

        $this->assertDatabaseMissing('regencies', ['id' => $regency->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('regencies.index'))->assertRedirect(route('login'));
        $this->get(route('regencies.create'))->assertRedirect(route('login'));
        $this->post(route('regencies.store'), [])->assertRedirect(route('login'));
        $this->get(route('regencies.edit', 1))->assertRedirect(route('login'));
        $this->put(route('regencies.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('regencies.destroy', 1))->assertRedirect(route('login'));
    });
});
