<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @property-read int $id */
/** @property-read string $name */
/** @property-read string $email */
/** @property-read string|null $avatar */
/** @property-read string|null $avatar_url */
/** @property-read Carbon|null $email_verified_at */
/** @property-read Collection $roles */
/** @property-read bool $is_system */
/** @property-read Carbon $created_at */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar_url,
            'email_verified_at' => $this->email_verified_at,
            'is_system' => $this->is_system,
            'roles' => $this->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
