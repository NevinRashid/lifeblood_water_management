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
            'status'                    => ['in:active,inactive, damaged, under_repair'],
            'type'                      => ['in:tanker,water tap'],
            'distribution_network_id'   => ['nullable', 'integer','exists:distribution_networks,id'],
            'location'                  => ['required','array'],
            'location.lat'              => ['required','numeric','between:-90,90'],
            'location.lng'              => ['required','numeric','between:-180,180'],
            'name'                      => ['sometimes','array','min:1'],
            'name.*'                    => ['sometimes','string','unique:distribution_points,name->*','max:255'],
        ];
    }

}
