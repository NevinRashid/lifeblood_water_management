<?php

namespace Modules\WaterDistributionOperations\Http\Requests\DeliveryRoute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeliveryRouteRequest extends FormRequest
{
    /**
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     *
     * @return array{description: string, end_time: string, name: string, path: string, path.*.lat: string, path.*.lng: string, planned_date: string, start_time: string, status: array<string|\Illuminate\Validation\Rules\In>, user_tanker_id: string}
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['planned', 'in_progress', 'completed', 'cancelled'])],
            'start_time' => 'nullable|date_format:Y-m-d H:i:s',
            'end_time' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:start_time',
            'user_tanker_id' => 'required|integer|exists:user_tanker,id',
            'planned_date' => 'nullable|date',
            'path' => 'nullable|array',
            'path.*.lat' => 'required_with:path|numeric|between:-90,90',
            'path.*.lng' => 'required_with:path|numeric|between:-180,180',


            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255',

            
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ];
    }
}
