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
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'path.type'                 => ['nullable', 'in:LineString'],
            'path.coordinates'          => ['nullable', 'array', 'min:2'],
            'path.coordinates.*'        => ['array', 'size:2'],
            'path.coordinates.*.0'      => ['numeric'], //lng
            'path.coordinates.*.1'      => ['numeric'], //lat
            'distribution_network_id'   => ['nullable', 'integer', 'exists:distribution_networks,id'],
            'current_pressure'          => ['nullable', 'numeric'],
            'current_flow'              => ['nullable', 'numeric'],
            'name'                      => ['sometimes','array','min:1'],
            'name.*'                    => ['sometimes','string','unique:pipes,name->*','max:255'],
        ];
    }
}
