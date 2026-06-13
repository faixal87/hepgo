<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Property */
class PropertySummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ringkasan = Str::limit(strip_tags((string) $this->description), 140);

        return [
            'id' => $this->id,
            'tajuk' => $this->title,
            'slug' => Str::slug($this->title),
            'kawasan' => $this->area?->name,
            'kategori' => $this->category?->name,
            'harga' => (float) $this->price,
            'harga_label' => 'RM'.number_format((float) $this->price, 0).' sebulan',
            'deposit' => $this->deposit !== null ? (float) $this->deposit : null,
            'deposit_label' => $this->deposit !== null ? 'RM'.number_format((float) $this->deposit, 0) : 'Tiada deposit dinyatakan',
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'jarak_km' => $this->distance_km !== null ? (float) $this->distance_km : null,
            'jarak_label' => $this->distance_km !== null
                ? 'Jarak anggaran: '.number_format((float) $this->distance_km, 1).' km dari POLIMAS'
                : 'Jarak anggaran dari POLIMAS belum dinyatakan',
            'keutamaan_penyewa' => $this->gender_preference?->label(),
            'thumbnail' => $this->thumbnailUrl(),
            'maps_url' => $this->maps_url,
            'direction_url' => $this->direction_url,
            'whatsapp_url' => $this->whatsappUrl(),
            'ringkasan' => $ringkasan,
            'penerangan_ringkas' => $ringkasan,
            'kemudahan_ringkas' => $this->summaryFacilities(),
            'created_at_label' => $this->created_at?->format('d/m/Y'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function summaryFacilities(): array
    {
        $booleanFacilities = collect([
            'Parking' => $this->has_parking,
            'WiFi' => $this->has_wifi,
            'Mesin Basuh' => $this->has_washing_machine,
            'Dapur' => $this->has_kitchen,
            'Penyaman Udara' => $this->has_aircond,
        ])->filter()->keys();

        return $this->facilities
            ->pluck('name')
            ->merge($booleanFacilities)
            ->unique()
            ->values()
            ->all();
    }
}
