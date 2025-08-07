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
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'type'                      => ['in:tanker,water tap'],
            'distribution_network_id'   => ['required', 'integer', 'exists:distribution_networks,id'],
            'location'                  => ['required', 'array'],
            'location.lat'              => ['required', 'numeric', 'between:-90,90'],
            'location.lng'              => ['required', 'numeric', 'between:-180,180'],
            'name'                      => ['required','array','min:1'],
            'name.*'                    => ['required','string','unique:distribution_points,name->*','max:255'],
        ];
    }

}
