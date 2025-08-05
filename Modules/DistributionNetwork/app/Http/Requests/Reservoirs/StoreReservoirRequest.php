<?php

namespace Modules\DistributionNetwork\Http\Requests\Reservoirs;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreReservoirRequest extends FormRequest
{
   public function authorize(): bool
    {
        return $this->user()->hasRole(['Super Admin', 'Network Manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:reservoirs,name',
            'location' => 'required|array',
            'location.latitude' => 'required|numeric',
            'location.longitude' => 'required|numeric',
            'tank_type' => ['required', 'string', Rule::in(['main', 'sub'])],
            'maximum_capacity' => 'required|numeric|gt:0',
            'minimum_critical_level' => 'required|numeric|gt:0|lt:maximum_capacity',
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'damaged', 'under_repair'])],
            'distribution_network_id' => 'required|integer|exists:distribution_networks,id',
        ];
    }
     protected function passedValidation(): void
    {
        if ($this->has('location') &&
            isset($this->location['latitude']) &&
            isset($this->location['longitude']))
        {
            $this->merge([
                'location' => new Point(
                    (float) $this->input('location.latitude'),
                    (float) $this->input('location.longitude')
                ),
            ]);
        }
}
}
