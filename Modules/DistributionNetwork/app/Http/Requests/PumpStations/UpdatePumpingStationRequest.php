<?php

namespace Modules\DistributionNetwork\Http\Requests\PumpStations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UpdatePumpingStationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|array',
            'location.lat' => 'required_with:location|numeric|between:-90,90',
            'location.lng' => 'required_with:location|numeric|between:-180,180',
            'status' => 'sometimes|in:active,inactive,damaged,under_repair',
            'distribution_network_id' => 'sometimes|exists:distribution_networks,id',
            'current_pressure' => 'nullable|numeric',
            'current_flow' => 'nullable|numeric',
            'max_flow' => 'nullable|numeric',
            'min_flow' => 'nullable|numeric',
            'max_pressure' => 'nullable|numeric',
            'min_pressure' => 'nullable|numeric',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
}
