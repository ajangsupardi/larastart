<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read int $regency_id
 * @property-read RegencyResource|null $regency
 * @property-read Carbon $created_at
 */
class DistrictResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'regency' => new RegencyResource($this->whenLoaded('regency')),
            'regency_id' => $this->regency_id,
            'created_at' => $this->created_at,
        ];
    }
}
