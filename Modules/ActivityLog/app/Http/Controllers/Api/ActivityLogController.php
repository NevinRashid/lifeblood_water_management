<?php

namespace Modules\ActivityLog\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\ActivityLog\Http\Requests\FilterActivityLogRequest;
use Modules\ActivityLog\Services\ActivityLogService;

class ActivityLogController extends Controller
{
    /**
     * Service to handle activityLog-related logic 
     * and separating it from the controller
     * 
     * @var ActivityLogService
     */
    protected $activityLogService;

    /**
     * ActivityLogController constructor
     *
     * @param ActivityLogService $activityLogService
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        // Inject the ActivityLogService to handle activityLog-related logic
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterActivityLogRequest $request)
    {
        $filters = $request->validated();
        $activityLogs = $this->activityLogService->getAll($filters);
        return $this->successResponse('Activity Logs Shown Successfully', $activityLogs);
    }
}
