<?php

namespace App\Http\Resources;

use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PropertyImage */
class PropertyImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->mediumUrl(),
            'thumbnail_url' => $this->thumbnailUrl(),
            'large_url' => $this->largeUrl(),
            'kapsyen' => $this->caption,
            'gambar_utama' => (bool) $this->is_thumbnail,
            'susunan' => $this->sort_order,
        ];
    }
}
