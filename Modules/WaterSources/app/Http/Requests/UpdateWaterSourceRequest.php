<?php

namespace Modules\WaterSources\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UpdateWaterSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => ['sometimes', 'required', Rule::in(['well', 'river', 'lake', 'dam', 'spring', 'desalination', 'imported'])],
            'location.latitude' => 'sometimes|required|numeric|between:-90,90',
            'location.longitude' => 'sometimes|required|numeric|between:-180,180',
            'capacity_per_day' => 'nullable|numeric|min:0',
            'capacity_per_hour' => 'nullable|numeric|min:0',
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'damaged', 'under_repair'])],
            'operating_date' => 'nullable|date',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:20480',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:10240',
            'videos' => 'nullable|array|max:3',
            'videos.*' => 'file|mimes:mp4,mov,avi,qt|max:102400',

            'name' => 'sometimes|array|min:1',
            'name.*' => 'sometimes|string|max:255',
        ];
    }

    protected function passedValidation(): void
{
    if ($this->has(['location.latitude', 'location.longitude'])) {
        $this->merge([
            'location' => new Point(
                (float) $this->input('location.latitude'),
                (float) $this->input('location.longitude')
            ),
        ]);
    }
}

}
