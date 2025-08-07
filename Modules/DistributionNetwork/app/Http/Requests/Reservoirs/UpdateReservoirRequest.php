<?php

namespace Modules\DistributionNetwork\Http\Requests\Reservoirs;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservoirRequest extends FormRequest
{
public function authorize(): bool
    {
        return $this->user()->hasRole(['Admin', 'Network Manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $reservoirId = $this->reservoir->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('reservoirs', 'name')->ignore($reservoirId),
            ],

            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',

            'tank_type' => ['sometimes', 'string', Rule::in(['main', 'sub'])],

            'maximum_capacity' => 'sometimes|numeric|gt:0',
            'minimum_critical_level' => 'sometimes|numeric|gt:0',

            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive', 'damaged', 'under_repair'])],

            'distribution_network_id' => 'sometimes|integer|exists:distribution_networks,id',
        ];
    }
}
