<?php
namespace Modules\WaterDistributionOperations\Http\Requests\RouteDelivery;
use Illuminate\Foundation\Http\FormRequest;

class StoreRouteDeliveryRequest extends FormRequest
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
            'water_amount_delivered' => 'required|numeric|min:0',
            'arrival_time' => 'nullable|date',
            'notes' => 'nullable|string',
            'distribution_point_id' => 'required|integer|exists:distribution_points,id',
        ];
    }
}
