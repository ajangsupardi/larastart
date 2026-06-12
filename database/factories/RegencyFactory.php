<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Regency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Regency>
 */
class RegencyFactory extends Factory
{
    protected $model = Regency::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'province_id' => Province::factory(),
            'created_by' => null,
        ];
    }
}
