<?php

namespace Modules\WaterDistributionOperations\Http\Requests\Tankers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTankerRequest extends FormRequest
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
     * @return array{current_location: string, current_location.lat: string, current_location.lng: string, last_maintenance_date: string, license_plate: array<string|\Illuminate\Validation\Rules\Unique>, max_capacity: string, next_maintenance_date: string, note: string, status: \Illuminate\Validation\Rules\In[]}
     */
    public function rules(): array
    {

        $tankerId = $this->route('tanker')->id;

        return [

            'license_plate' => [ 'string', 'max:255', Rule::unique('tankers')->ignore($tankerId)],
            'max_capacity' => 'numeric|min:0',
            'status' => [ Rule::in(['available', 'on_route', 'in_maintenance', 'out_of_service'])],
            'last_maintenance_date' => 'nullable|date_format:Y-m-d',
            'next_maintenance_date' => 'nullable|date_format:Y-m-d|after_or_equal:last_maintenance_date',
            'current_location' => 'nullable|array',
            'current_location.lat' => 'current_location|numeric',
            'current_location.lng' => 'current_location|numeric',

            'note' => 'nullable|array',
            'note.*' => 'nullable|string',
        ];
    }
}
