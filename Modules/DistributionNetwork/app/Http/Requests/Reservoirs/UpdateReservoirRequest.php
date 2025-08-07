<?php

namespace Modules\DistributionNetwork\Http\Requests\Reservoirs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UpdateReservoirRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'array'],
            'location.lat' => ['required_with:location', 'numeric', 'between:-90,90'],
            'location.lng' => ['required_with:location', 'numeric', 'between:-180,180'],
            'tank_type' => ['sometimes', 'in:main,sub'],
            'maximum_capacity' => ['sometimes', 'numeric', 'min:0'],
            'minimum_critical_level' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:active,inactive,damaged,under_repair'],
            'distribution_network_id' => ['sometimes', 'exists:distribution_networks,id'],
        ];
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

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reservoir = $this->route('reservoir');
        return Gate::allows('update_distribution_network_component', $reservoir);
    }
}
