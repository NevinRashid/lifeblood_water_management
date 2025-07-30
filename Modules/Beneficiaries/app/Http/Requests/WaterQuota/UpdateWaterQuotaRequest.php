<?php

namespace Modules\Beneficiaries\Http\Requests\WaterQuota;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\WaterQuotaStatus;

class UpdateWaterQuotaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'received_volume' => 'sometimes|numeric|min:1|max:999999999999.9999',

            'allocation_date' => 'sometimes|date|after_or_equal:now',

            'status' => [
                'required',
                Rule::in(WaterQuotaStatus::all()),
            ],

            'notes' => 'nullable|string|max:5000',

            'beneficiary_id' => 'sometimes|exists:beneficiaries,id',

            'delivery_route_id' => 'sometimes|exists:delivery_routes,id',
        ];
    }

    public function attributes()
    {
        return [
            'received_volume' => 'Received Volume',
            'allocation_date' => 'Allocation Date',
            'status' => 'Status',
            'notes' => 'Notes',
            'beneficiary_id' => 'Beneficiary',
            'delivery_route_id' => 'Delivery Route',
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => 'The :attribute must be a valid decimal number',
            'min' => 'The :attribute cannot be negative, must be at least :min',
            'after_or_equal' => 'The :attribute must be in the future or now',
            'status.in' => 'The status must be one of ' . implode(', ', WaterQuotaStatus::all()),
            'exists' => 'The selected :attribute does not exist',
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
