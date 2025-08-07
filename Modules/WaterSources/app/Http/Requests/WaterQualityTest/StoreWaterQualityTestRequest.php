<?php

namespace Modules\WaterSources\Http\Requests\WaterQualityTest;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterQualityTestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'water_source_id' => 'required|exists:water_sources,id',
            'ph_level' => 'nullable|numeric',
            'dissolved_oxygen' => 'nullable|numeric',
            'total_dissolved_solids' => 'nullable|numeric',
            'turbidity' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'chlorine' => 'nullable|numeric',
            'nitrate' => 'nullable|numeric',
            'total_coliform_bacteria' => 'nullable|numeric',
            'test_date' => 'required|date_format:Y-m-d H:i:s',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
