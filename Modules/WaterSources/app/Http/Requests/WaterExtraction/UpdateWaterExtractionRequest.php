<?php

namespace Modules\WaterSources\Http\Requests\WaterExtraction;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateWaterExtractionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'extracted' => 'sometimes|numeric|min:0.0001|max:9999999999999.9999',
            'extraction_date'     => 'sometimes|date|before_or_equal:now',
            'water_source_id'     => 'sometimes|exists:water_sources,id',
            'distribution_network_id' => 'sometimes|exists:distribution_networks,id',
        ];
    }

    public function attributes()
    {
        return [
            'extracted' => 'Extracted',
            'extraction_date' => 'Extraction Date',
            'water_source_id' => 'Water Source'
        ];
    }

    public function messages()
    {
        return [
            'numeric' => 'the :attribute should be numeric',
            'exists' => 'the :attribute is not exist',
            'before_or_equal' => 'the :attribute should be before or equal now',
            'min' => 'the :attribute should be at least :min',
            'max' => 'the :attribute should be at most :max',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
