<?php

namespace Modules\WaterSources\Http\Requests\WaterExtraction;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWaterExtractionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'extracted' => 'required|numeric|min:0.0001|max:9999999999999.9999',
            'extraction_date'     => 'required|date|before_or_equal:now',
            'water_source_id'     => 'required|exists:water_sources,id',
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
            'required' => 'the :attribute should not be empty, please add the :attribute!',
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
