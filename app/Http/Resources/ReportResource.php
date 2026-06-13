<?php

namespace App\Http\Resources;

use App\Models\PropertyReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PropertyReport */
class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdminRoute = str_starts_with((string) $request->route()?->getName(), 'api.v1.admin.');

        return [
            'id' => $this->id,
            'rumah_sewa' => $this->whenLoaded('property', fn (): ?array => $this->property ? [
                'id' => $this->property->id,
                'tajuk' => $this->property->title,
            ] : null),
            'nama_pengadu' => $this->reporter_name,
            'no_telefon_pengadu' => $this->reporter_phone,
            'emel_pengadu' => $this->reporter_email,
            'jenis_aduan' => $this->report_type?->label(),
            'jenis_aduan_kod' => $this->report_type?->value,
            'mesej_aduan' => $this->message,
            'status_aduan' => $this->status?->label(),
            'status_aduan_kod' => $this->status?->value,
            'dikendalikan_oleh' => $this->when($isAdminRoute, $this->handledBy?->name),
            'tarikh_dikendalikan' => $this->when($isAdminRoute, $this->handled_at?->toISOString()),
            'catatan_admin' => $this->when($isAdminRoute, $this->admin_remarks),
            'tarikh_aduan' => $this->created_at?->toISOString(),
        ];
    }
}
