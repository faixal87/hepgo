<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\ReportStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('resolve reports') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                ReportStatus::REVIEWING->value,
                ReportStatus::RESOLVED->value,
                ReportStatus::REJECTED->value,
            ])],
            'admin_remarks' => ['nullable', 'string'],
        ];
    }
}
