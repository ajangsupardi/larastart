<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Village>
 */
class VillageFactory extends Factory
{
    protected $model = Village::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->citySuffix().' Village',
            'district_id' => District::factory(),
            'postal_code' => fake()->numerify('#####'),
            'created_by' => null,
        ];
    }
}
