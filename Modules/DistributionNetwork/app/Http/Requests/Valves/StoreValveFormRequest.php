<?php

namespace Modules\DistributionNetwork\Http\Requests\Valves;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreValveFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location' => 'required|array',
            'location.lat' => 'required|numeric',
            'location.lng' => 'required|numeric',
            'is_open' => 'required|boolean',
            'valve_type' => 'required|in:gate_valve,butterfly_valve,ball_valve',
            'status' => 'required|in:active,inactive,damaged,under_repair',
            'distribution_network_id' => 'required|exists:distribution_networks,id',
            'current_flow' => 'nullable|numeric',
            'max_flow' => 'nullable|numeric',
            'min_flow' => 'nullable|numeric',

            'name' => 'required|array|min:1',
            'name.*' => 'required|string|unique:valves,name->*|max:255',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // Convert to Point after validation
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => ' Data verification error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
