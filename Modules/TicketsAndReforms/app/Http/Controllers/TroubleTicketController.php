<?php

namespace Modules\TicketsAndReforms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TicketsAndReforms\Http\Requests\TroubleTicket\StoreTroubleTicketRequest;
use Modules\TicketsAndReforms\Http\Requests\TroubleTicket\UpdateTroubleTickeStatusRequest;
use Modules\TicketsAndReforms\Http\Requests\TroubleTicket\UpdateTroubleTicketRequest;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use Modules\TicketsAndReforms\Services\TroubleTicketService;

class TroubleTicketController extends Controller
{

    protected TroubleTicketService $troubleTicketService;

    /**
     * Constructor for the TroubleTicketController class.
     * Initializes the $troubleTicketService property via dependency injection.
     *
     * @param TroubleTicketService $troubleTicketService
     */
    public function __construct(TroubleTicketService $troubleTicketService)
    {
        $this->troubleTicketService =$troubleTicketService;
    }

    /**
     * This method return all troubleTickets from database.
     * using the pointService via the getAllTroubles method
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['subject','status', 'user_id']);
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->troubleTicketService->getAllTroubles($filters)
                            ,200);
    }

    /**
     * Add a new troubleTicket the database using the troubleTicketService via the createTrouble method
     * passes the validated request data to createTrouble.
     *
     * @param StoreTroubleTicketRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTroubleTicketRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->troubleTicketService->createTrouble($request->validated())
                            ,201);
    }

    /**
     * Get troubleTicket from database.
     * using the troubleTicketService via the showTrouble method
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->troubleTicketService->showTrouble($troubleTicket)
                            ,200);
    }

    /**
     * Update a troubleTicket in the database using the troubleTicketService via the updateTrouble method.
     * passes the validated request data to updateTrouble.
     *
     * @param UpdateTroubleTickeRequest $request
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTroubleTicketRequest $request, TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        ,$this->troubleTicketService->updateTrouble($request->validated(),$troubleTicket));
    }

    /**
     * Remove the specified troubleTicket from database.
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TroubleTicket $troubleTicket)
    {
        $this->troubleTicketService->deleteTrouble($troubleTicket);
        return $this->successResponse(
                        'Deleted succcessful'
                        , null);
    }

    /**
     * Change the status of the troubleTicket.
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function changeTroubleStatus(UpdateTroubleTickeStatusRequest $request,TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        , $this->troubleTicketService->changeStatus($request->validated(),$troubleTicket));
    }

    /**
     * Get all troubles reported by citizens
     *
     * @return \Illuminate\Http\Response
     */
    public function getCitizenTroubles()
    {
        return $this->successResponse(
                        'Operation succcessful'
                        , $this->troubleTicketService->getAllCitizenTroubles());
    }

    /**
     * Get all complaints reported by citizens
     *
     * @return \Illuminate\Http\Response
     */
    public function getCitizenComplaints()
    {
        return $this->successResponse(
                        'Operation succcessful'
                        , $this->troubleTicketService->getAllCitizenComplaints());
    }

    /**
     * .
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function approveTrouble(TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                        'The complaint has been confirmed and has become a troubleTicket awaiting reform'
                        , $this->troubleTicketService->approveTrouble($troubleTicket));
    }

    /**
     * .
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsReviewed(TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                        'The complaint has been reviewed '
                        , $this->troubleTicketService->reviewComplaint($troubleTicket));
    }

    /**
     *
     *
     * @param TroubleTicket $troubleTicket
     *
     * @return \Illuminate\Http\Response
     */
    public function reject(TroubleTicket $troubleTicket)
    {
        return $this->successResponse(
                        'The report was rejected'
                        , $this->troubleTicketService->rejectTrouble($troubleTicket));
    }
}
