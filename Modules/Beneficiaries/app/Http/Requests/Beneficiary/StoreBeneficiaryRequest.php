<?php

namespace Modules\Beneficiaries\Http\Requests\Beneficiary;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Beneficiaries\Enums\BeneficiaryStatus;
use Modules\Beneficiaries\Enums\BeneficiaryType;

class StoreBeneficiaryRequest extends FormRequest
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
            'distribution_point_id' => 'required|exists:distribution_points,id',
            'user_id' => 'required|exists:users,id',

            'household_size' => 'required|integer|min:1|max:50',
            'children_count' => 'nullable|integer|min:0|max:20',
            'elderly_count' => 'nullable|integer|min:0|max:20',
            'disabled_count' => 'nullable|integer|min:0|max:20',

            'benefit_type' => ['required', Rule::in(BeneficiaryType::all())],

            'status' => ['required', Rule::in(BeneficiaryStatus::all())],

            'location' => 'required|array',
            'location.latitude' => 'required|numeric',
            'location.longitude' => 'required|numeric',

            'address' => 'required|string|max:255',
            'additional_data' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    // Convert to Point after validation
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['location'] = new Point($data['location']['latitude'], $data['location']['longitude']);
        return $data;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
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
            'required' => 'The :attribute is required',
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
