<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('edit properties') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'owner_id' => ['sometimes', 'required', 'integer', 'exists:owners,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'address' => ['sometimes', 'required', 'string'],
            'area_id' => ['sometimes', 'required', 'integer', 'exists:areas,id'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'maps_url' => ['nullable', 'url', 'max:255'],
            'status' => ['nullable', Rule::in(PropertyAvailabilityStatus::values())],
            'verification_status' => ['nullable', Rule::in(VerificationStatus::values())],
            'gender_preference' => ['nullable', Rule::in(GenderPreference::values())],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'total_bathrooms' => ['nullable', 'integer', 'min:0'],
            'max_occupants' => ['nullable', 'integer', 'min:0'],
            'has_parking' => ['nullable', 'boolean'],
            'has_wifi' => ['nullable', 'boolean'],
            'has_washing_machine' => ['nullable', 'boolean'],
            'has_kitchen' => ['nullable', 'boolean'],
            'has_aircond' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', 'exists:facilities,id'],
        ];
    }
}
