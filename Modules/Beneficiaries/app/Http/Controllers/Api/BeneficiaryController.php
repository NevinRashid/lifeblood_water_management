<?php

namespace Modules\Beneficiaries\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Beneficiaries\Http\Requests\Beneficiary\FilterBeneficiaryRequest;
use Modules\Beneficiaries\Http\Requests\Beneficiary\StoreBeneficiaryRequest;
use Modules\Beneficiaries\Http\Requests\Beneficiary\UpdateBeneficiaryRequest;
use Modules\Beneficiaries\Services\BeneficiaryService;

class BeneficiaryController extends Controller
{

    /**
     * Service to handle beneficiary-related logic 
     * and separating it from the controller
     * 
     * @var BeneficiaryService
     */
    protected $beneficiaryService;

    /**
     * BeneficiaryController constructor
     *
     * @param BeneficiaryService $beneficiaryService
     */
    public function __construct(BeneficiaryService $beneficiaryService)
    {
        // Inject the BeneficiaryService to handle beneficiary-related logic
        $this->beneficiaryService = $beneficiaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterBeneficiaryRequest $request)
    {
        $filters = $request->validated();
        $data = $this->beneficiaryService->getAll($filters);
        return $this->successResponse('Beneficaries Shown Successfully', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeneficiaryRequest $request)
    {
        $data = $request->validated();
        $beneficiary = $this->beneficiaryService->store($data);

        return $this->successResponse('Beneficiary Added Successfully', $beneficiary, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $data = $this->beneficiaryService->get($id);
        return $this->successResponse('Beneficiary Shown Successfully', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBeneficiaryRequest $request, $id)
    {
        $data = $request->validated();
        $beneficiary = $this->beneficiaryService->update($data, $id);

        return $this->successResponse('Beneficiary Updated Successfully', $beneficiary);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->beneficiaryService->destroy($id);
        return $this->successResponse('Beneficiary Deleted Successfully');
    }
}
