<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
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
            'name' => $this->name,
            'nama' => $this->name,
            'email' => $this->email,
            'emel' => $this->email,
            'phone' => $this->phone,
            'no_telefon' => $this->phone,
            'profile_photo_url' => $this->profilePhotoUrl(),
            'tema_paparan' => $this->uiTheme()['label'],
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'roles' => $this->getRoleNames()->values(),
            'permissions' => $this->getAllPermissions()->pluck('name')->values(),
        ];
    }
}
