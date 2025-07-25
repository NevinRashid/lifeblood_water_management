<?php

namespace Modules\Sensors\Http\Requests\Sensor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Sensors\Rules\UniqueSensorTypePerEntity;

class UpdateSensorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $sensorId = $this->route('id');

        $rules = [
            'device_id' => [
                'sometimes',
                'string',
                Rule::unique('sensors', 'device_id')->ignore($sensorId)
            ],
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|array',
            'location.latitude' => 'required_with:location|numeric|between:-90,90',
            'location.longitude' => 'required_with:location|numeric|between:-180,180',
            'sensor_type' => [
                'sometimes',
                Rule::in(['pressure_sensor', 'flow_sensor', 'level_sensor', 'quality_sensor']),
            ],
            'status' => [
                'sometimes',
                Rule::in(['active', 'inactive', 'faulty', 'under_maintenance'])
            ],
            'sensorable_id' => 'sometimes|string',
            'sensorable_type' => 'sometimes|string|in:valve,pipe,pumpingstation'
        ];
        
        // Only apply the unique rule if all required values are present
        if ($this->filled(['sensor_type', 'sensorable_type', 'sensorable_id'])) {
            $rules['sensor_type'][] = new UniqueSensorTypePerEntity(
                $this->input('sensorable_type'),
                $this->input('sensorable_id'),
                $this->route('sensor')?->id // or $this->sensor?->id
            );
        }
        return $rules ;
    }

    // Convert validated data (handles null/missing/valid location)
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['location'])) {
            // Convert to Point if location is provided
            $data['location'] = new Point(
                $data['location']['lat'],
                $data['location']['lng']
            );
        } else {
            // Explicitly set to null if field was sent as null
            // (Missing field leaves existing location unchanged)
            if ($this->has('location')) {
                $data['location'] = null;
            }
        }

        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => ' Data verification error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
