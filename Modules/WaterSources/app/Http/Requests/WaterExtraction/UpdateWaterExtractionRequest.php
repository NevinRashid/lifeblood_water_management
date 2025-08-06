<?php

namespace Modules\WaterSources\Http\Requests\WaterExtraction;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateWaterExtractionRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('update_water_extraction');
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'extracted' => 'sometimes|numeric|min:0.0001|max:9999999999999.9999',
            'water_source_id'     => 'sometimes|exists:water_sources,id',
        ];
    }

    public function attributes()
    {
        return [
            'extracted' => 'Extracted',
            'water_source_id' => 'Water Source'
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
