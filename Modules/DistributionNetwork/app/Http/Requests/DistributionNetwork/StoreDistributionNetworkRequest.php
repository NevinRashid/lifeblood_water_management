<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionNetwork;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\DistributionNetwork\Rules\VaildPolygon;

class StoreDistributionNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('create_distribution_network');
    }

    public function rules(): array
    {
        return [
            'zone'                    => ['required', 'array', new VaildPolygon()],
            'zone.*.lat'              => ['required_with:zone', 'numeric', 'between:-90,90'],
            'zone.*.lng'              => ['required_with:zone', 'numeric', 'between:-180,180'],
            'loss_percentage'         => ['required', 'numeric', 'between:0,100'],
            'current_volume'          => ['nullable', 'numeric', 'min:0'],

            'water_source_id'         => ['required', 'integer', 'exists:water_sources,id'],
            'manager_id'              => ['required', 'integer', 'exists:users,id'],

            'address' => 'required|array|min:1',
            'address.*' => 'required|string|max:255',

            'name' => 'required|array|min:1',
            'name.*' => 'required|string|unique:distribution_networks,name->*|max:255',
        ];
    }
}
