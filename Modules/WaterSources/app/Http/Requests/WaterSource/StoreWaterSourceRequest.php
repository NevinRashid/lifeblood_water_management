<?php

namespace Modules\WaterSources\Http\Requests\WaterSource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreWaterSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',
            'source' => ['required', Rule::in(['well', 'river', 'lake', 'dam', 'spring', 'desalination', 'imported'])],
            'location' => 'required|array',
            'location.latitude' => 'required|numeric',
            'location.longitude' => 'required|numeric',
            'capacity_per_day' => 'nullable|numeric|min:0',
            'capacity_per_hour' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['active', 'inactive', 'damaged', 'under_repair'])],
            'operating_date' => 'nullable|date',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:20480',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:10240',
            'videos' => 'nullable|array|max:3',
            'videos.*' => 'file|mimes:mp4,mov,avi,qt|max:102400',
        ];
    }

    /**
     * Prepare the data for validation.
     * This method combines latitude and longitude into a Point object after validation.
     */
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






