<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::when($this->filled('min_price'), ['gte:min_price']),
            ],
            'status' => ['nullable', Rule::in([
                PropertyAvailabilityStatus::AVAILABLE->value,
                PropertyAvailabilityStatus::FULL->value,
            ])],
            'gender_preference' => ['nullable', Rule::in(GenderPreference::values())],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', 'exists:facilities,id'],
            'sort' => ['nullable', Rule::in([
                'latest',
                'price_low',
                'price_high',
                'distance_near',
                'terbaru',
                'price_asc',
                'price_desc',
                'distance',
            ])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'search' => 'carian',
            'area_id' => 'kawasan',
            'category_id' => 'kategori',
            'min_price' => 'harga minimum',
            'max_price' => 'harga maksimum',
            'status' => 'status',
            'gender_preference' => 'keutamaan penyewa',
            'facilities' => 'kemudahan',
            'sort' => 'susunan',
            'per_page' => 'bilangan setiap halaman',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'max_price.gte' => 'Harga maksimum mesti sama atau lebih tinggi daripada harga minimum.',
            'per_page.max' => 'Bilangan setiap halaman tidak boleh melebihi :max rekod.',
        ];
    }
}
