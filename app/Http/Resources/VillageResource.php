<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $postal_code
 * @property-read int $district_id
 * @property-read DistrictResource|null $district
 * @property-read Carbon $created_at
 */
class VillageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'postal_code' => $this->postal_code,
            'district' => new DistrictResource($this->whenLoaded('district')),
            'district_id' => $this->district_id,
            'created_at' => $this->created_at,
        ];
    }
}
