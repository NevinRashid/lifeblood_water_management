<?php

namespace Modules\Beneficiaries\Http\Requests\WaterQuota;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\WaterQuotaStatus;

class FilterWaterQuotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('view_water_quota');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'received_volume' => 'nullable|numeric|min:0|max:999999999999.9999',

            'allocation_date' => 'nullable|date',

            'status' => [
                'nullable',
                Rule::in(WaterQuotaStatus::all()),
            ],

            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'delivery_route_id' => 'nullable|exists:delivery_routes,id',

            'sort_by' => 'nullable|string|in:received_volume,allocation_date,status',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|numeric|min:5|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'received_volume.numeric' => 'The water volume must be a valid decimal.',
            'received_volume.min'     => 'The water volume cannot be negative.',

            'allocation_date.date' => 'The allocation date must be a valid date.',

            'status.in' => 'Status must be one of: ' . implode(', ', WaterQuotaStatus::all()),

            'notes.string' => 'Notes must be a string.',
            'notes.max'    => 'Notes must not exceed 5000 characters.',

            'beneficiary_id.exists' => 'Selected beneficiary does not exist.',
            'delivery_route_id.exists' => 'Selected delivery route does not exist.',

            'sort_by.in' => 'Invalid sort column.',
            'sort_direction.in' => 'Sort direction must be asc or desc.',
        ];
    }

    /**
     * Customize failed validation response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed for water quota data',
            'errors' => $validator->errors()
        ], 422));
    }
}
