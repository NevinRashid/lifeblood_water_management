<?php

namespace Modules\WaterSources\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\WaterSources\Services\HeatmapService;

class HeatmapController extends Controller
{
    protected HeatmapService $heatmapService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_risk_heatmap', only:['index']),
        ];
    }

    /**
     * Constructor for the HeatmapController class.
     * Initializes the $heatmapService property via dependency injection.
     *
     * @param HeatmapService $heatmapService
     */
    public function __construct(HeatmapService $heatmapService)
    {
        $this->heatmapService =$heatmapService;
    }

    /**
     * Return a list of risky water sources for the heatmap.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->heatmapService->getRiskySources()
                            ,200);
    }
}
