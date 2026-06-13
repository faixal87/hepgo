<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Property */
class PropertyResource extends JsonResource
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
            'tajuk' => $this->title,
            'penerangan' => $this->description,
            'alamat' => $this->address,
            'kawasan' => $this->area?->name,
            'kategori' => $this->category?->name,
            'harga' => (float) $this->price,
            'deposit' => $this->deposit !== null ? (float) $this->deposit : null,
            'status' => $this->status?->label(),
            'status_kod' => $this->status?->value,
            'status_pengesahan' => $this->verification_status?->label(),
            'jarak_km' => $this->distance_km !== null ? (float) $this->distance_km : null,
            'label_jarak' => 'Jarak anggaran dari POLIMAS',
            'keutamaan_penyewa' => $this->gender_preference?->label(),
            'keutamaan_penyewa_kod' => $this->gender_preference?->value,
            'bilangan_bilik' => $this->total_rooms,
            'bilangan_bilik_air' => $this->total_bathrooms,
            'maksimum_penghuni' => $this->max_occupants,
            'kemudahan_ringkas' => [
                'parking' => (bool) $this->has_parking,
                'wifi' => (bool) $this->has_wifi,
                'mesin_basuh' => (bool) $this->has_washing_machine,
                'dapur' => (bool) $this->has_kitchen,
                'penyaman_udara' => (bool) $this->has_aircond,
            ],
            'kemudahan' => FacilityResource::collection($this->whenLoaded('facilities')),
            'thumbnail' => $this->thumbnailUrl(),
            'gambar' => PropertyImageResource::collection($this->whenLoaded('images')),
            'maps_url' => $this->mapsUrl(),
            'direction_url' => $this->direction_url,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'whatsapp_url' => $this->whatsappUrl(),
            'pemilik' => $this->whenLoaded('owner', fn (): array => [
                'nama' => $this->owner?->name,
                'no_whatsapp' => $this->owner?->whatsapp_number,
                'no_telefon' => $this->owner?->phone,
            ]),
            'tarikh_dicipta' => $this->created_at?->toISOString(),
            'tarikh_dikemaskini' => $this->updated_at?->toISOString(),
        ];
    }
}
