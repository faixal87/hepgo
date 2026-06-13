<?php

namespace App\Http\Resources;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Area */
class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->name,
            'penerangan' => $this->description,
            'jarak_dari_polimas_km' => $this->distance_from_campus !== null ? (float) $this->distance_from_campus : null,
            'status' => $this->status?->label(),
        ];
    }
}
