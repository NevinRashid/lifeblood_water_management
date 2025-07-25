<?php

namespace Modules\Sensors\Http\Requests\Sensor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Sensors\Rules\UniqueSensorTypePerEntity;

class StoreSensorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'device_id' => 'required|string|unique:sensors,device_id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|array',
            'location.lat' => 'required_with:location|numeric|between:-90,90',
            'location.lng' => 'required_with:location|numeric|between:-180,180',
            'sensor_type' => [
                'required',
                Rule::in(['pressure_sensor', 'flow_sensor', 'level_sensor', 'quality_sensor']),
                new UniqueSensorTypePerEntity(
                    $this->sensorable_type,
                    $this->sensorable_id,
                )
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'faulty', 'under_maintenance'])
            ],
            'sensorable_id' => 'required|string',
            'sensorable_type' => 'required|string|in:valve,pipe,pumpingstation',
        ];
    }


    // Convert to Point after validation
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
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
