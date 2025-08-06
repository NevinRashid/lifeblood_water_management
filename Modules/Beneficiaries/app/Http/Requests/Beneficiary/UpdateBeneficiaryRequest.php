<?php

namespace Modules\Beneficiaries\Http\Requests\Beneficiary;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Beneficiaries\Enums\BeneficiaryStatus;
use Modules\Beneficiaries\Enums\BeneficiaryType;

class UpdateBeneficiaryRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('update_beneficiary');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'distribution_point_id' => 'sometimes|exists:distribution_points,id',

            'household_size' => 'sometimes|integer|min:1|max:50',
            'children_count' => 'sometimes|integer|min:0|max:20',
            'elderly_count' => 'sometimes|integer|min:0|max:20',
            'disabled_count' => 'sometimes|integer|min:0|max:20',

            'benefit_type' => ['sometimes', Rule::in(BeneficiaryType::all())],

            'status' => ['sometimes', Rule::in(BeneficiaryStatus::all())],

            'location' => 'sometimes|array',
            'location.latitude' => 'sometimes|numeric',
            'location.longitude' => 'sometimes|numeric',

            'address' => 'sometimes|array|min:1',
            'address.*' => 'sometimes|string|max:255',

            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:1000',

            'additional_data' => 'nullable|array',
            'additional_data.*' => 'nullable|string|max:1000',
        ];
    }


    public function validated($key = null, $default = null)
    {
        if ($this->has(['location.latitude', 'location.longitude'])) {
            $data = parent::validated();
            $data['location'] = new Point($data['location']['latitude'], $data['location']['longitude']);
            return $data;
        }
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'distribution_point_id' => 'Distribution point',
            'household_size' => 'Household size',
            'children_count' => 'Number of children',
            'elderly_count' => 'Number of elderly',
            'disabled_count' => 'Number of disabled individuals',
            'benefit_type' => 'Benefit type',
            'status' => 'Status',
            'location' => 'Location',
            'address' => 'Address',
            'additional_data' => 'Additional data',
            'notes' => 'Notes',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'exists' => 'The selected :attribute is invalid',
            'in' => 'The :attribute must be one of the allowed values',
            'integer' => 'The :attribute must be an integer number',
            'min' => 'The :attribute must be at least :min',
            'max' => 'The :attribute must not exceed :max',
            'json' => 'The :attribute must be a valid JSON',
        ];
    }

    /**
     * Customize failed validation response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed for beneficiary data',
            'errors' => $validator->errors()
        ], 422));
    }
}
