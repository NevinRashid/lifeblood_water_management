<?php

namespace Modules\WaterSources\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\WaterSources\Services\WaterExtractionReportService;

class WaterExtractionReportController extends Controller
{
    protected $reportService;

    public function __construct(WaterExtractionReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate water extraction report
     *
     * @queryParam period_type required daily|monthly|annual|custom
     * @queryParam group_by required network|source
     * @queryParam date optional The specific date (Y-m-d) for daily/monthly/annual
     * @queryParam start_date optional Start date for custom period (Y-m-d)
     * @queryParam end_date optional End date for custom period (Y-m-d)
     * @queryParam network_id optional Filter by a specific network
     *
     * @response {
     *   "success": true,
     *   "data": [...]
     * }
     */
    public function generateReport(Request $request)
    {
      
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'period_type' => 'required|in:daily,monthly,annual,custom',
            'group_by' => 'required|in:network,source',
            'date' => 'required_if:period_type,daily,monthly,annual|date_format:Y-m-d',
            'start_date' => 'required_if:period_type,custom|date_format:Y-m-d',
            'end_date' => 'required_if:period_type,custom|date_format:Y-m-d|after_or_equal:start_date',
            'network_id' => 'nullable|integer|exists:distribution_networks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // 2. Prepare dates
            $date = $request->filled('date') ? Carbon::parse($request->date) : null;
            $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
            $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

            // 3. Call service
            $report = $this->reportService->generateReport(
                $request->period_type,
                $request->group_by,
                $startDate,
                $endDate,
                $date,
                $request->network_id
            );

            // 4. Return JSON response
            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        }
    }
}
