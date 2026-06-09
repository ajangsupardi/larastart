<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @property-read int $id */
/** @property-read string $name */
/** @property-read string $email */
/** @property-read Carbon|null $email_verified_at */
/** @property-read Collection $roles */
/** @property-read Carbon $created_at */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'roles' => $this->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
