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
        $user=Auth::user();
        return $user->can('create_distribution_network');
    }

    public function rules(): array
    {
        return [
            'name'                    => ['required', 'string','unique:distribution_networks', 'max:255'],
            'address'                 => ['nullable', 'string','max:255'],
            'water_source_id'         => ['required', 'integer','exists:water_sources,id'],
            'manager_id'              => ['required','integer','exists:users,id'],
            'zone'                    => ['required','array',new VaildPolygon()],
            'zone.*.lat'              => ['required_with:zone','numeric','between:-90,90'],
            'zone.*.lng'              => ['required_with:zone','numeric','between:-180,180'],
            'loss_percentage'         => ['required', 'numeric', 'between:0,100'],
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
            'name.required'                  => 'The name is required please.',
            'name.max'                       => 'The length of the name may not be more than 255 characters.',
            'name.unique'                    => 'The name must be unique and not duplicate. Please use another name',
            'address.max'                    => 'The length of the address may not be more than 255 characters.',
            'water_source_id.required'       => 'This distribution network must be connected to a specific water source.',
            'water_source_id.exists'         => 'The water source you are trying to connect this distribution network to does not exist.',
            'zone.required'                  => 'The zone is required please.',
            'zone.array'                     => 'The zone must be an array of points.',
            'zone.*.lat.required'            => 'The latitude is required please.',
            'zone.*.lng.required'            => 'The longitude is required please.',
            'zone.*.lat.numeric'             => 'The latitude must be a numeric value.',
            'zone.*.lng.numeric'             => 'The longitude must be a numeric value.',
        ];
    }
}
