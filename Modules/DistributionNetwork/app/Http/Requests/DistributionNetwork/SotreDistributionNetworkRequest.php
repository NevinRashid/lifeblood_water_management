<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionNetwork;

use Illuminate\Foundation\Http\FormRequest;
use Modules\DistributionNetwork\Rules\VaildPolygon;

class SotreDistributionNetworkRequest extends FormRequest
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
            'water_source_id'         => ['required', 'integer', 'exists:water_sources,id'],
            'zone.type'               => ['nullable', 'in:Polygon'],
            'zone.coordinates'        => ['array', 'size:1'],
            'zone.coordinates.0'      => ['array', 'min:4', new VaildPolygon()],
            'zone.coordinates.0.*'    => ['array', 'size:2'],
            'zone.coordinates.0.*.0'  => ['numeric'], //lng
            'zone.coordinates.0.*.1'  => ['numeric'], //lat
            'manager_id'              => ['required', 'integer', 'exists:users,id'],

            'address' => 'required|array|min:1',
            'address.*' => 'required|string|max:255',

            'name' => 'required|array|min:1',
            'name.*' => 'required|string|unique:distribution_networks,name->*|max:255',
        ];
    }
}
