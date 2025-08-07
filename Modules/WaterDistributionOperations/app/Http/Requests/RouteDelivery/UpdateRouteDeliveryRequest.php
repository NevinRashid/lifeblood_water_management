<?php
namespace Modules\WaterDistributionOperations\Http\Requests\RouteDelivery;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRouteDeliveryRequest extends FormRequest
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
     * @return array{arrival_time: string, distribution_point_id: string, notes: string, water_amount_delivered: string}
     */
    public function rules(): array
    {
        return [
            'water_amount_delivered' => 'sometimes|required|numeric|min:0',
            'arrival_time' => 'sometimes|nullable|date',
            'distribution_point_id' => 'sometimes|required|integer|exists:distribution_points,id',

            'notes' => 'sometimes|nullable|array',
            'notes.*' => 'sometimes|nullable|string',
        ];
    }
}
