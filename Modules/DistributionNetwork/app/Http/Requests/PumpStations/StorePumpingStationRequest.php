<?php

namespace Modules\DistributionNetwork\Http\Requests\PumpStations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\DistributionNetwork;

class StorePumpingStationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,inactive,damaged,under_repair',
            'distribution_network_id' => 'required|exists:distribution_networks,id',
            'current_pressure' => 'nullable|numeric',
            'current_flow' => 'nullable|numeric',
            'max_flow' => 'nullable|numeric',
            'min_flow' => 'nullable|numeric',
            'max_pressure' => 'nullable|numeric',
            'min_pressure' => 'nullable|numeric',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $networkId = $this->input('distribution_network_id');
        $network = DistributionNetwork::find($networkId);

        if (! $network) {
            return false;
        }

        return Gate::allows('create_distribution_network_component', $network);
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
