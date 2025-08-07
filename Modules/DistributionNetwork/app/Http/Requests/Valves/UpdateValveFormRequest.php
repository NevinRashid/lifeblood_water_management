<?php

namespace Modules\DistributionNetwork\Http\Requests\Valves;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UpdateValveFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location' => 'sometimes|array',
            'location.lat' => 'required_with:location|numeric',
            'location.lng' => 'required_with:location|numeric',
            'is_open' => 'sometimes|boolean',
            'valve_type' => 'sometimes|in:gate_valve,butterfly_valve,ball_valve',
            'status' => 'sometimes|in:active,inactive,damaged,under_repair',
            'distribution_network_id' => 'sometimes|exists:distribution_networks,id',
            'current_flow' => 'nullable|numeric',
            'max_flow' => 'nullable|numeric',
            'min_flow' => 'nullable|numeric',

            'name' => 'sometimes|array|min:1',
            'name.*' => 'sometimes|string|unique:valves,name->*|max:255',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $valve = $this->route('valve'); 
        return Gate::allows('update_distribution_network_component',$valve);
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
}
