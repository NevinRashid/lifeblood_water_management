<?php

namespace Modules\Beneficiaries\Http\Requests\WaterQuota;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\WaterQuotaStatus;

class StoreWaterQuotaRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('create_water_quota');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'received_volume' => 'required|numeric|min:0|max:999999999999.9999',

            'allocation_date' => 'required|date|after_or_equal:now',

            'status' => [
                'required',
                Rule::in(WaterQuotaStatus::all()),
            ],

            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:1000',

            'beneficiary_id' => 'required|exists:beneficiaries,id',

            'delivery_route_id' => 'required|exists:delivery_routes,id',
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
