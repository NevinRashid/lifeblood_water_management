<?php

namespace Modules\DistributionNetwork\Http\Requests\DistributionNetwork;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\DistributionNetwork\Rules\VaildPolygon;

class UpdateDistributionNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('update_distribution_network');
    }

    public function rules(): array
    {
        return [
            'zone'                    => ['nullable', 'array', new VaildPolygon()],
            'zone.*.lat'              => ['nullable_with:zone', 'numeric', 'between:-90,90'],
            'zone.*.lng'              => ['nullable_with:zone', 'numeric', 'between:-180,180'],
            'loss_percentage'         => ['nullable', 'numeric', 'between:0,100'],
            'current_volume'          => ['nullable', 'numeric', 'min:0'],
            'manager_id'              => ['sometimes', 'integer', 'exists:users,id'],

            'address' => 'sometimes|array|min:1',
            'address.*' => 'sometimes|string|max:255',

            'name' => 'sometimes|array|min:1',
            'name.*' => 'sometimes|string|unique:distribution_networks,name->*|max:255',
        ];
    }
}
