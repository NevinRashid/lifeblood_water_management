<?php

namespace Modules\DistributionNetwork\Http\Requests\Pipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user=Auth::user();
        return $user->can('create_distribution_network_component');
    }

    public function rules(): array
    {
        return [
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'distribution_network_id'   => ['required', 'integer','exists:distribution_networks,id'],
            'current_pressure'          => ['nullable', 'numeric'],
            'current_flow'              => ['nullable', 'numeric'],
            'path'                      => ['required','array'],
            'path.*.lat'                => ['required_with:path','numeric','between:-90,90'],
            'path.*.lng'                => ['required_with:path','numeric','between:-180,180'],

            'name' => 'required|array|min:1',
            'name.*' => 'required|string|unique:pipes,name->*|max:255',

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
