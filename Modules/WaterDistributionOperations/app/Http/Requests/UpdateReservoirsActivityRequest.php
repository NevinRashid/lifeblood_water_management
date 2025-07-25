<?php

namespace Modules\WaterDistributionOperations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservoirsActivityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'activity_level'  => 'sometimes|required|numeric|min:0',
            'activity_time'   => 'sometimes|required|date|before_or_equal:now',
            'amount'          => 'nullable|numeric|min:0',
            'triggered_by'    => 'sometimes|required|in:manual_user,scada_system',
            'activity_type'   => 'sometimes|required|in:filling_started,filling_ended,emptying_started,emptying_ended,overflow_detected,critical_low_level,level_restored_above_critical,manual_adjustment,pump_started_scada,valve_opened_scada',
            'notes'           => 'nullable|string',
            'reservoir_id'    => 'sometimes|required|exists:reservoirs,id',
            'user_id'         => 'nullable|exists:users,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
