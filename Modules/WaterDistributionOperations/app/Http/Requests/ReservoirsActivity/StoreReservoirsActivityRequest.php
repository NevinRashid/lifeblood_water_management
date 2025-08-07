<?php

namespace Modules\WaterDistributionOperations\Http\Requests\ReservoirsActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReservoirsActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('create_reservoir_activity');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'activity_level'  => 'required|numeric|min:0',
            'activity_time'   => 'required|date|after_or_equal:now',
            'amount'          => 'nullable|numeric|min:0',
            'triggered_by'    => 'required|in:manual_user,scada_system',
            'activity_type'   => 'required|in:filling_started,filling_ended,emptying_started,emptying_ended,overflow_detected,critical_low_level,level_restored_above_critical,manual_adjustment,pump_started_scada,valve_opened_scada',
            'notes'           => 'nullable|string',
            'reservoir_id'    => 'required|exists:reservoirs,id',
            'user_id'         => 'nullable|exists:users,id',
        ];
    }

}
