<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
