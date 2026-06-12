<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Regency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->citySuffix().' District',
            'regency_id' => Regency::factory(),
            'created_by' => null,
        ];
    }
}
