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
        ];
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages():array
    {
        return[
            'name.max'                       => 'The length of the name may not be more than 255 characters.',
            'name.unique'                    => 'The name must be unique and not duplicate. Please use another name',
            'address.max'                    => 'The length of the address may not be more than 255 characters.',
            'zone.type.in'                   => 'The zone type must be "Polygon".',
            'zone.coordinates.array'         => 'The coordinates must be an array.',
            'zone.coordinates.size'          => 'Only one set of coordinates (outer ring) is allowed.',
            'zone.coordinates.0.array'       => 'The outer ring must be an array of points.',
            'zone.coordinates.0.min'         => 'The outer ring must contain at least 4 points to form a closed polygon.',
            'zone.coordinates.0.*.array'     => 'Each point in the outer ring must be an array.',
            'zone.coordinates.0.*.size'      => 'Each point must have exactly 2 values (longitude and latitude).',
            'zone.coordinates.0.*.0.numeric' => 'Longitude (x) must be a number.',
            'zone.coordinates.0.*.1.numeric' => 'Latitude (y) must be a number.',
        ];
    }


}
