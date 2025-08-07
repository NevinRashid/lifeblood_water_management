<?php

namespace Modules\DistributionNetwork\Http\Requests\Reservoirs;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\DistributionNetwork;

class StoreReservoirRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location' => ['required', 'array'], // Expecting ['lat' => ..., 'lng' => ...]
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],
            'tank_type' => ['required', 'in:main,sub'],
            'maximum_capacity' => ['required', 'numeric', 'min:0'],
            'minimum_critical_level' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,damaged,under_repair'],
            'distribution_network_id' => ['required', 'exists:distribution_networks,id'],
            'name'          => ['required','array','min:1'],
            'name.*'        => ['required','string','unique:reservoirs,name->*','max:255'],
        ];
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

        return Gate::allows('create_distributon_network_component', $network);
    }
}
