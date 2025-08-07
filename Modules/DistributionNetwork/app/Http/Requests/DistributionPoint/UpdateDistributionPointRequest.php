<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionPoint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdateDistributionPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $point = $this->route('point');
        return Gate::allows('update_distribution_network_component', $point);
    }

    public function rules(): array
    {
        return [
            'name'                      => ['nullable', 'string', 'unique:distribution_points', 'max:255'],
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'type'                      => ['in:tanker,water tap'],
            'distribution_network_id'   => ['nullable', 'integer', 'exists:distribution_networks,id'],
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
            'name.max'                          => 'The length of the name may not be more than 255 characters.',
            'name.unique'                       => 'The name must be unique and not duplicate. Please use another name',
            'status.in'                         => 'The status must be one of (active,inactive,damaged,under_repair)',
            'type.in'                           => 'The type must be one of (tanker,water tap)',
            'distribution_network_id.exists'    => 'The network you are trying to connect this distribution point to does not exist.',
            'location.array'                    => 'The location must be an array.',
            'location.lat.numeric'              => 'The longitude must be a numeric value.',
            'location.lng.numeric'              => 'The latitude must be a numeric value.',
        ];
    }
}
