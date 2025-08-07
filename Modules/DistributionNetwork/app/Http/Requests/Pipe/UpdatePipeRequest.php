<?php

namespace Modules\DistributionNetwork\Http\Requests\Pipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdatePipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pipe = $this->route('pipe');
        return Gate::allows('update_distribution_network_component', $pipe);
    }

    public function rules(): array
    {
        return [
            'name'                      => ['nullable', 'string', 'unique:pipes', 'max:255'],
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'path.type'                 => ['nullable', 'in:LineString'],
            'path.coordinates'          => ['nullable', 'array', 'min:2'],
            'path.coordinates.*'        => ['array', 'size:2'],
            'path.coordinates.*.0'      => ['numeric'], //lng
            'path.coordinates.*.1'      => ['numeric'], //lat
            'distribution_network_id'   => ['nullable', 'integer', 'exists:distribution_networks,id'],
            'current_pressure'          => ['nullable', 'numeric'],
            'current_flow'              => ['nullable', 'numeric'],

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
            'path.type.in'                      => 'The path type must be "LineString".',
            'path.coordinates.array'            => 'The coordinates must be an array of points.',
            'path.coordinates.min'              => 'The coordinates must contains at least two points.',
            'path.coordinates.*.array'          => 'Each point must be an array of two numeric values (longitude and latitude).',
            'path.coordinates.*.size'           => 'Each point must contain exactly two values.',
            'path.coordinates*.0.numeric'       => 'The longitude must be a numeric value.',
            'path.coordinates*.1.numeric'       => 'The latitude must be a numeric value.',
            'distribution_network_id.exists'    => 'The network you are trying to connect this pipe to does not exist.',
            'current_pressure.numeric'          => 'The pressure must be a number.',
            'current_flow.numeric'              => 'The flow must be a number.',

        ];
    }
}
