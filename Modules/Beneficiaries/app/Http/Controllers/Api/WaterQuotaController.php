<?php

namespace Modules\Beneficiaries\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Beneficiaries\Http\Requests\WaterQuota\FilterWaterQuotaRequest;
use Modules\Beneficiaries\Http\Requests\WaterQuota\UpdateWaterQuotaRequest;
use Modules\Beneficiaries\Http\Requests\WaterQuota\StoreWaterQuotaRequest;
use Modules\Beneficiaries\Services\WaterQuotaService;

class WaterQuotaController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:view_water_quota', only: ['show']),
            new Middleware('can:delete_water_quota', only: ['destroy']),
        ];
    }

    /**
     * Service to handle waterQuota-related logic 
     * and separating it from the controller
     * 
     * @var WaterQuotaService
     */
    protected $waterQuotaService;

    /**
     * WaterQuotaController constructor
     *
     * @param WaterQuotaService $waterQuotaService
     */
    public function __construct(WaterQuotaService $waterQuotaService)
    {
        // Inject the BeneficiaryService to handle waterQuota-related logic
        $this->waterQuotaService = $waterQuotaService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(FilterWaterQuotaRequest $request)
    {
        $filters = $request->validated();
        $waterQuotas = $this->waterQuotaService->getAll($filters);
        return $this->successResponse('Water Quotas Shown Successfully', $waterQuotas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterQuotaRequest $request)
    {
        $data = $request->validated();

        $waterQuota = $this->waterQuotaService->store($data);

        return $this->successResponse('Water Quota Addedd Successfully', $waterQuota, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $waterQuota = $this->waterQuotaService->get($id);
        return $this->successResponse('Water Quota Shown Successfully', $waterQuota);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterQuotaRequest $request, $id)
    {
        $data = $request->validated();
        $waterQuota = $this->waterQuotaService->update($data, $id);
        return $this->successResponse('Water Quota Updated Successfully', $waterQuota);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->waterQuotaService->destroy($id);
        return $this->successResponse('Water Quota Deleted Successfully');
    }
}
