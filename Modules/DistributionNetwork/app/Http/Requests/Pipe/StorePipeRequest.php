<?php

namespace Modules\DistributionNetwork\Http\Requests\Pipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\DistributionNetwork\Models\DistributionNetwork;

class StorePipeRequest extends FormRequest
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
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'distribution_network_id'   => ['required', 'integer', 'exists:distribution_networks,id'],
            'current_pressure'          => ['nullable', 'numeric'],
            'current_flow'              => ['nullable', 'numeric'],
            'path'                      => ['required','array'],
            'path.*.lat'                => ['required_with:path','numeric','between:-90,90'],
            'path.*.lng'                => ['required_with:path','numeric','between:-180,180'],
            'name'                      => ['required','array','min:1'],
            'name.*'                    => ['required','string','unique:pipes,name->*','max:255'],
        ];
    }
}
