<?php

namespace Modules\DistributionNetwork\Http\Requests\Pipe;

use Illuminate\Foundation\Http\FormRequest;

class StorePipeRequest extends FormRequest
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
            'name'                      => ['required', 'string','unique:pipes', 'max:255'],
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            /*'path.type'                 => ['required','in:LineString'],
            'path.coordinates'          => ['required','array','min:2'],
            'path.coordinates.*'        => ['array','size:2'],
            'path.coordinates.*.0'      => ['numeric'],//lng
            'path.coordinates.*.1'      => ['numeric'],//lat*/
            'distribution_network_id'   => ['required', 'integer','exists:distribution_networks,id'],
            'current_pressure'          => ['nullable', 'numeric'],
            'current_flow'              => ['nullable', 'numeric'],
            'path'                      => ['required','array'],
            'path.*.lat'                => ['required_with:path','numeric','between:-90,90'],
            'path.*.lng'                => ['required_with:path','numeric','between:-180,180'],
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
            'name.required'                     => 'The name is required please.',
            'name.max'                          => 'The length of the name may not be more than 255 characters.',
            'name.unique'                       => 'The name must be unique and not duplicate. Please use another name',
            'status.in'                         => 'The status must be one of (active,inactive,damaged,under_repair)',
            'path.required'                     => 'The path is required please.',
            'path.array'                        => 'The path must be an array of points.',
            'path.*.lat.required'               => 'The latitude is required please.',
            'path.*.lng.required'               => 'The longitude is required please.',
            'path.*.lat.numeric'                => 'The latitude must be a numeric value.',
            'path.*.lng.numeric'                => 'The longitude must be a numeric value.',
            'distribution_network_id.required'  => 'This pipe must be connected to a specific network.',
            'distribution_network_id.exists'    => 'The network you are trying to connect this pipe to does not exist.',
            'current_pressure.numeric'          => 'The pressure must be a number.',
            'current_flow.numeric'              => 'The flow must be a number.',

        ];
    }

}
