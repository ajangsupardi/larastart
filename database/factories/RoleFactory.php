<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    private static array $definedNames = [];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Administrator',
            'Editor',
            'Moderator',
            'Contributor',
            'Viewer',
        ]);

        $slug = str($name)->slug();

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->sentence(),
            'permissions' => [
                'users' => fake()->randomElements(['create', 'read', 'update', 'delete'], rand(1, 4)),
                'roles' => fake()->randomElements(['create', 'read', 'update', 'delete'], rand(0, 4)),
            ],
        ];
    }

    public function withFullPermissions(): static
    {
        return $this->state(fn () => [
            'permissions' => [
                'users' => ['create', 'read', 'update', 'delete'],
                'roles' => ['create', 'read', 'update', 'delete'],
            ],
        ]);
    }
}
