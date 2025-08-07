<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionPoint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\DistributionNetwork\Models\DistributionNetwork;

class StoreDistributionPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $networkId = $this->input('distribution_network_id');
        $network = DistributionNetwork::find($networkId);

        if (! $network) {
            return false;
        }

        return Gate::allows('create_distribution_network_component', $network);
    }

    public function rules(): array
    {
        return [
            'name'                      => ['required', 'string', 'unique:distribution_points', 'max:255'],
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'type'                      => ['in:tanker,water tap'],
            'distribution_network_id'   => ['required', 'integer', 'exists:distribution_networks,id'],
            'location'                  => ['required', 'array'],
            'location.lat'              => ['required', 'numeric', 'between:-90,90'],
            'location.lng'              => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'                     => 'The name is required please.',
            'name.max'                          => 'The length of the name may not be more than 255 characters.',
            'name.unique'                       => 'The name must be unique and not duplicate. Please use another name',
            'status.in'                         => 'The status must be one of (active,inactive,damaged,under_repair)',
            'type.in'                           => 'The type must be one of (tanker,water tap)',
            'distribution_network_id.required'  => 'This distribution point must be connected to a specific network.',
            'distribution_network_id.exists'    => 'The network you are trying to connect this distribution point to does not exist.',
            'location.required'                 => 'The location is required please.',
            'location.array'                    => 'The location must be an array.',
            'location.lat.numeric'              => 'The longitude must be a numeric value.',
            'location.lng.numeric'              => 'The latitude must be a numeric value.',

        ];
    }
}
