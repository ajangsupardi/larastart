<?php

use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('ProvinceController', function () {
    beforeEach(function () {
        $role = Role::factory()->withFullPermissions()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->withoutMiddleware(PreventRequestForgery::class);
    });

    it('can list provinces', function () {
        Province::factory(3)->create();

        $this->get(route('provinces.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Provinces/Index')
                ->has('provinces.data', 3)
                ->has('filters')
                ->has('stats'));
    });

    it('can search provinces by name', function () {
        Province::factory()->create(['name' => 'DKI Jakarta']);
        Province::factory()->create(['name' => 'Jawa Barat']);

        $this->get(route('provinces.index', ['search' => 'Jakarta']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Provinces/Index')
                ->has('provinces.data', 1));
    });

    it('can search provinces by code', function () {
        Province::factory()->create(['code' => '31']);
        Province::factory()->create(['code' => '32']);

        $this->get(route('provinces.index', ['search' => '31']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Provinces/Index')
                ->has('provinces.data', 1));
    });

    it('shows create form', function () {
        $this->get(route('provinces.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Provinces/Create'));
    });

    it('can create a province', function () {
        $this->post(route('provinces.store'), [
            'name' => 'Bali',
            'code' => '51',
        ])->assertRedirect(route('provinces.index'));

        $this->assertDatabaseHas('provinces', [
            'name' => 'Bali',
            'code' => '51',
        ]);
    });

    it('validates required fields when creating', function () {
        $this->post(route('provinces.store'), [])
            ->assertSessionHasErrors(['name', 'code']);
    });

    it('validates unique code when creating', function () {
        Province::factory()->create(['code' => '31']);

        $this->post(route('provinces.store'), [
            'name' => 'Test',
            'code' => '31',
        ])->assertSessionHasErrors(['code']);
    });

    it('shows edit form', function () {
        $province = Province::factory()->create();

        $this->get(route('provinces.edit', $province))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Provinces/Edit')
                ->has('province'));
    });

    it('can update a province', function () {
        $province = Province::factory()->create();

        $this->put(route('provinces.update', $province), [
            'name' => 'Updated Province',
            'code' => '99',
        ])->assertRedirect(route('provinces.index'));

        $this->assertDatabaseHas('provinces', [
            'id' => $province->id,
            'name' => 'Updated Province',
            'code' => '99',
        ]);
    });

    it('validates unique code when updating', function () {
        $province = Province::factory()->create(['code' => '31']);
        $other = Province::factory()->create(['code' => '32']);

        $this->put(route('provinces.update', $province), [
            'name' => 'Updated',
            'code' => '32',
        ])->assertSessionHasErrors(['code']);
    });

    it('allows same code when updating own record', function () {
        $province = Province::factory()->create(['code' => '31']);

        $this->put(route('provinces.update', $province), [
            'name' => 'Updated',
            'code' => '31',
        ])->assertRedirect(route('provinces.index'));
    });

    it('can delete a province', function () {
        $province = Province::factory()->create();

        $this->delete(route('provinces.destroy', $province))
            ->assertRedirect(route('provinces.index'));

        $this->assertDatabaseMissing('provinces', ['id' => $province->id]);
    });

    it('requires authentication', function () {
        auth()->logout();

        $this->get(route('provinces.index'))->assertRedirect(route('login'));
        $this->get(route('provinces.create'))->assertRedirect(route('login'));
        $this->post(route('provinces.store'), [])->assertRedirect(route('login'));
        $this->get(route('provinces.edit', 1))->assertRedirect(route('login'));
        $this->put(route('provinces.update', 1), [])->assertRedirect(route('login'));
        $this->delete(route('provinces.destroy', 1))->assertRedirect(route('login'));
    });
});
