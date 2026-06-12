<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read int $province_id
 * @property-read ProvinceResource|null $province
 * @property-read Carbon $created_at
 */
class RegencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'province' => new ProvinceResource($this->whenLoaded('province')),
            'province_id' => $this->province_id,
            'created_at' => $this->created_at,
        ];
    }
}
