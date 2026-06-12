<?php

use App\Models\District;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('VillageController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list villages', function () {
        Village::factory(3)->create();

        $this->get(route('villages.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Index')
                ->has('villages.data', 3)
                ->has('filters')
                ->has('districts')
                ->has('stats'));
    });

    it('can search villages by name', function () {
        Village::factory()->create(['name' => 'Cibiru']);
        Village::factory()->create(['name' => 'Coblong']);

        $this->get(route('villages.index', ['search' => 'Cibiru']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Index')
                ->has('villages.data', 1));
    });

    it('can search villages by postal code', function () {
        Village::factory()->create(['postal_code' => '40615']);
        Village::factory()->create(['postal_code' => '40115']);

        $this->get(route('villages.index', ['search' => '40615']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Index')
                ->has('villages.data', 1));
    });

    it('can filter villages by district', function () {
        $district = District::factory()->create();
        Village::factory()->create(['district_id' => $district->id]);
        Village::factory()->create();

        $this->get(route('villages.index', ['district_id' => $district->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Index')
                ->has('villages.data', 1));
    });

    it('shows create form with districts', function () {
        $this->get(route('villages.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Create')
                ->has('districts'));
    });

    it('can create a village', function () {
        $district = District::factory()->create();

        $this->post(route('villages.store'), [
            'name' => 'Cibiru Wetan',
            'district_id' => $district->id,
            'postal_code' => '40615',
        ])->assertRedirect(route('villages.index'));

        $this->assertDatabaseHas('villages', [
            'name' => 'Cibiru Wetan',
            'district_id' => $district->id,
            'postal_code' => '40615',
        ]);
    });

    it('can create a village without postal code', function () {
        $district = District::factory()->create();

        $this->post(route('villages.store'), [
            'name' => 'Cibiru Wetan',
            'district_id' => $district->id,
        ])->assertRedirect(route('villages.index'));

        $this->assertDatabaseHas('villages', [
            'name' => 'Cibiru Wetan',
            'district_id' => $district->id,
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('villages.store'), [])
            ->assertSessionHasErrors(['name', 'district_id']);
    });

    it('validates district exists when creating', function () {
        $this->post(route('villages.store'), [
            'name' => 'Test',
            'district_id' => 9999,
        ])->assertSessionHasErrors('district_id');
    });

    it('shows edit form with village data', function () {
        $village = Village::factory()->create();

        $this->get(route('villages.edit', $village))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Villages/Edit')
                ->has('village')
                ->has('districts'));
    });

    it('can update a village', function () {
        $village = Village::factory()->create();
        $newDistrict = District::factory()->create();

        $this->put(route('villages.update', $village), [
            'name' => 'Updated Village',
            'district_id' => $newDistrict->id,
            'postal_code' => '99999',
        ])->assertRedirect(route('villages.index'));

        $this->assertDatabaseHas('villages', [
            'id' => $village->id,
            'name' => 'Updated Village',
            'district_id' => $newDistrict->id,
            'postal_code' => '99999',
        ]);
    });

    it('can delete a village', function () {
        $village = Village::factory()->create();

        $this->delete(route('villages.destroy', $village))
            ->assertRedirect(route('villages.index'));

        $this->assertDatabaseMissing('villages', ['id' => $village->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('villages.index'))->assertRedirect(route('login'));
        $this->get(route('villages.create'))->assertRedirect(route('login'));
        $this->post(route('villages.store'), [])->assertRedirect(route('login'));
        $this->get(route('villages.edit', 1))->assertRedirect(route('login'));
        $this->put(route('villages.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('villages.destroy', 1))->assertRedirect(route('login'));
    });
});
