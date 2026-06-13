<?php

namespace App\Http\Requests;

use App\Enums\ReportType;
use App\Models\Property;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePropertyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'reporter_name' => ['nullable', 'string', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:30'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
            'report_type' => ['required', Rule::in(ReportType::values())],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'rumah sewa',
            'reporter_name' => 'nama pengadu',
            'reporter_phone' => 'no. telefon pengadu',
            'reporter_email' => 'emel pengadu',
            'report_type' => 'jenis aduan',
            'message' => 'mesej aduan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'report_type.required' => 'Sila pilih jenis aduan.',
            'message.required' => 'Sila masukkan mesej aduan.',
            'message.min' => 'Mesej aduan mesti sekurang-kurangnya :min aksara.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $propertyId = $this->input('property_id');

                if (blank($propertyId)) {
                    return;
                }

                $isPublic = Property::query()
                    ->whereKey($propertyId)
                    ->publiclyVisible()
                    ->exists();

                if (! $isPublic) {
                    $validator->errors()->add('property_id', 'Rumah sewa yang dipilih tidak tersedia untuk aduan awam.');
                }
            },
        ];
    }
}
