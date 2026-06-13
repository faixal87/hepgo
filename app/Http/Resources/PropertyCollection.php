<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PropertyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => PropertyResource::collection($this->collection)->resolve($request),
            'meta' => [
                'halaman_semasa' => $this->currentPage(),
                'halaman_akhir' => $this->lastPage(),
                'setiap_halaman' => $this->perPage(),
                'jumlah' => $this->total(),
            ],
            'links' => [
                'pertama' => $this->url(1),
                'akhir' => $this->url($this->lastPage()),
                'sebelum' => $this->previousPageUrl(),
                'seterusnya' => $this->nextPageUrl(),
            ],
        ];
    }
}
