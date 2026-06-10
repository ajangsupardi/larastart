<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read int $id */
/** @property-read string $name */
/** @property-read string $slug */
/** @property-read string|null $description */
/** @property-read array $permissions */
/** @property-read bool $is_system */
/** @property-read int $users_count */
/** @property-read Carbon $created_at */
class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'permissions' => $this->permissions,
            'is_system' => $this->is_system,
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at,
        ];
    }
}
