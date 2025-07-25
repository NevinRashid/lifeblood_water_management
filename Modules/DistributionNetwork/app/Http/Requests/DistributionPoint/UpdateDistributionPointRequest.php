<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionPoint;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDistributionPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                      => ['nullable', 'string','unique:distribution_points', 'max:255'],
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'type'                      => ['in:tanker,water tap'],
            'distribution_network_id'   => ['nullable', 'integer','exists:distribution_networks,id'],
            'location.type'             => ['nullable','in:Point'],
            'location.coordinates'      => ['nullable','array','size:2'],
            'location.coordinates.0'    => ['numeric'],//lng
            'location.coordinates.1'    => ['numeric'],//lat
        ];
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages():array
    {
        return[
            'name.max'                          => 'The length of the name may not be more than 255 characters.',
            'name.unique'                       => 'The name must be unique and not duplicate. Please use another name',
            'status.in'                         => 'The status must be one of (active,inactive,damaged,under_repair)',
            'type.in'                           => 'The type must be one of (tanker,water tap)',
            'distribution_network_id.exists'    => 'The network you are trying to connect this distribution point to does not exist.',
            'location.type.in'                  => 'The location type must be "Point".',
            'location.coordinates.array'        => 'The coordinates must be an array.',
            'location.coordinates.size'         => 'Each point must contain exactly two values.',
            'location.coordinates.0.numeric'    => 'The longitude must be a numeric value.',
            'location.coordinates.1.numeric'    => 'The latitude must be a numeric value.',
        ];
    }

}

