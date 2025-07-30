<?php

namespace Modules\WaterSources\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\WaterSources\Services\HeatmapService;

class HeatmapController extends Controller
{
    protected HeatmapService $heatmapService;

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
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->heatmapService->getRiskySources()
                            ,200);
    }
}
