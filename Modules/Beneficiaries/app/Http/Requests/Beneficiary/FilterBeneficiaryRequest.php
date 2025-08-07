<?php

namespace Modules\Beneficiaries\Http\Requests\Beneficiary;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\BeneficiaryStatus;
use Modules\Beneficiaries\Enums\BeneficiaryType;

class FilterBeneficiaryRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('view_any_beneficiaries');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'distribution_point_id' => 'nullable|exists:distribution_points,id',
            'user_id'               => 'nullable|exists:users,id',

            'benefit_type' => [
                'nullable',
                Rule::in(BeneficiaryType::all()),
            ],
            'status' => [
                'nullable',
                Rule::in(BeneficiaryStatus::all()),
            ],

            'address' => 'nullable|string|max:255',

            'household_size' => 'nullable|numeric|min:1',

            'has_children' => 'nullable|boolean',
            'has_elderly' => 'nullable|boolean',
            'has_disabled' => 'nullable|boolean',

            'sort_by'         => 'nullable|string|in:household_size,children_count,elderly_count,disabled_count',
            'sort_direction'  => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|numeric|min:11'
        ];
    }

    /**
     * if the validation failed it return a json response
     */
    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed Validate Data',
            'errors' => $validator->errors(),
        ], 422));
    }
}
