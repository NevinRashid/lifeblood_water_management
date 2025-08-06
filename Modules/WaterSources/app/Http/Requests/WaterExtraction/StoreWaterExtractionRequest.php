<?php

namespace Modules\WaterSources\Http\Requests\WaterExtraction;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWaterExtractionRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('create_water_extraction');
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'extracted' => 'required|numeric|min:0.0001|max:9999999999999.9999',
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

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['extraction_date'] = Carbon::now();
        return $data;
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
