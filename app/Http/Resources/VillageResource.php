<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
