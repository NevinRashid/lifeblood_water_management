<?php

namespace Modules\WaterSources\Http\Requests\WaterExtraction;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilterWaterExtractionRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('view_water_extraction');
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'water_source_id' => 'nullable|exists:water_sources,id',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'min_extracted'      => 'nullable|numeric|min:0',
            'max_extracted'      => 'nullable|numeric|min:0|gte:min_extracted',
            'sort_by'         => 'nullable|string|in:extraction_date,extracted',
            'sort_direction'  => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|numeric|min:11'
        ];
    }

    public function attributes()
    {
        return [
            'water_source_id' => 'Water Source',
            'start_date'      => 'Start Date',
            'end_date'        => 'End Date',
            'min_extracted'      => 'Min Extracted',
            'max_extracted'      => 'Max Extracted',
            'sort_by'          => 'Sort By Column',
            'sort_direction'   => 'Sort Direction',
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
