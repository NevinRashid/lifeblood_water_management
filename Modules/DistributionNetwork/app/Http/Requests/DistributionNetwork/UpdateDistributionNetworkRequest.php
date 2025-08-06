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
        $user=Auth::user();
        return $user->can('update_distribution_network');
    }

    public function rules(): array
    {
        return [
            'name'                    => ['nullable', 'string','unique:distribution_networks', 'max:255'],
            'address'                 => ['nullable', 'string','max:255'],
            'manager_id'              => ['sometimes','integer','exists:users,id'],
            'zone'                    => ['nullable','array',new VaildPolygon()],
            'zone.*.lat'              => ['nullable_with:zone','numeric','between:-90,90'],
            'zone.*.lng'              => ['nullable_with:zone','numeric','between:-180,180'],
            'loss_percentage'         => ['nullable', 'numeric', 'between:0,100'],
            'current_volume'          => ['nullable', 'numeric', 'min:0'],
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
            'name.max'                       => 'The length of the name may not be more than 255 characters.',
            'name.unique'                    => 'The name must be unique and not duplicate. Please use another name',
            'address.max'                    => 'The length of the address may not be more than 255 characters.',
            'zone.array'                     => 'The zone must be an array of points.',
            'zone.*.lat.numeric'             => 'The latitude must be a numeric value.',
            'zone.*.lng.numeric'             => 'The longitude must be a numeric value.',
            'loss_percentage'                => ['sometimes', 'numeric', 'between:0,100'],
            'current_volume'                 => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
