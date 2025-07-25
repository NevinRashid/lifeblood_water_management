<?php

namespace Modules\WaterDistributionOperations\Http\Requests\Tankers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTankerRequest extends FormRequest
{
    /**
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 
     * @return array{current_location: string, current_location.lat: string, current_location.lng: string, last_maintenance_date: string, license_plate: string, max_capacity: string, next_maintenance_date: string, note: string, status: array<string|\Illuminate\Validation\Rules\In>}
     */
    public function rules(): array
    {
        return [
            'license_plate' => 'required|string|max:255|unique:tankers,license_plate',
            'max_capacity' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['available', 'on_route', 'in_maintenance', 'out_of_service'])],
            'last_maintenance_date' => 'nullable|date_format:Y-m-d',
            'next_maintenance_date' => 'nullable|date_format:Y-m-d|after_or_equal:last_maintenance_date',
            'note' => 'nullable|string',
            'current_location' => 'nullable|array',
            'current_location.lat' => 'required_with:current_location|numeric',
            'current_location.lng' => 'required_with:current_location|numeric',
        ];
    }
}
