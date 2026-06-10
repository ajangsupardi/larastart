<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
