<?php

use App\Models\District;
use App\Models\Regency;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('DistrictController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list districts', function () {
        District::factory(3)->create();

        $this->get(route('districts.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Districts/Index')
                ->has('districts.data', 3)
                ->has('filters')
                ->has('regencies')
                ->has('stats'));
    });

    it('can search districts by name', function () {
        District::factory()->create(['name' => 'Bandung Timur']);
        District::factory()->create(['name' => 'Jakarta Selatan']);

        $this->get(route('districts.index', ['search' => 'Bandung']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Districts/Index')
                ->has('districts.data', 1));
    });

    it('can filter districts by regency', function () {
        $regency = Regency::factory()->create();
        District::factory()->create(['regency_id' => $regency->id]);
        District::factory()->create();

        $this->get(route('districts.index', ['regency_id' => $regency->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Districts/Index')
                ->has('districts.data', 1));
    });

    it('shows create form with regencies', function () {
        $this->get(route('districts.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Districts/Create')
                ->has('regencies'));
    });

    it('can create a district', function () {
        $regency = Regency::factory()->create();

        $this->post(route('districts.store'), [
            'name' => 'Bandung Timur',
            'regency_id' => $regency->id,
        ])->assertRedirect(route('districts.index'));

        $this->assertDatabaseHas('districts', [
            'name' => 'Bandung Timur',
            'regency_id' => $regency->id,
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('districts.store'), [])
            ->assertSessionHasErrors(['name', 'regency_id']);
    });

    it('validates regency exists when creating', function () {
        $this->post(route('districts.store'), [
            'name' => 'Test',
            'regency_id' => 9999,
        ])->assertSessionHasErrors('regency_id');
    });

    it('shows edit form with district data', function () {
        $district = District::factory()->create();

        $this->get(route('districts.edit', $district))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Districts/Edit')
                ->has('district')
                ->has('regencies'));
    });

    it('can update a district', function () {
        $district = District::factory()->create();
        $newRegency = Regency::factory()->create();

        $this->put(route('districts.update', $district), [
            'name' => 'Updated District',
            'regency_id' => $newRegency->id,
        ])->assertRedirect(route('districts.index'));

        $this->assertDatabaseHas('districts', [
            'id' => $district->id,
            'name' => 'Updated District',
            'regency_id' => $newRegency->id,
        ]);
    });

    it('can delete a district', function () {
        $district = District::factory()->create();

        $this->delete(route('districts.destroy', $district))
            ->assertRedirect(route('districts.index'));

        $this->assertDatabaseMissing('districts', ['id' => $district->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('districts.index'))->assertRedirect(route('login'));
        $this->get(route('districts.create'))->assertRedirect(route('login'));
        $this->post(route('districts.store'), [])->assertRedirect(route('login'));
        $this->get(route('districts.edit', 1))->assertRedirect(route('login'));
        $this->put(route('districts.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('districts.destroy', 1))->assertRedirect(route('login'));
    });
});
