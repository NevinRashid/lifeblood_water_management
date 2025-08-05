<?php

namespace Modules\TicketsAndReforms\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use LogicException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\TicketsAndReforms\Events\CitizenReportOfTroubleAccepted;
use Modules\TicketsAndReforms\Events\ComplaintReviewed;
use Modules\TicketsAndReforms\Events\TroubleRejected;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleTicketService
{
    use HandleServiceErrors;

    /**
     * Get all troubles from database with optional filtering.
     * If no filters are applied, results are cached per locale for one day.
     *
     * @param array $filters
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTroubles(array $filters = [])
    {
        try{
            if (!$filters) {
                return Cache::remember('all_troubles_'. app()->getLocale(), now()->addDay(), function(){
                    $troubles= TroubleTicket::with(['reporter','reform'])->paginate(15);
                    return $troubles->through(function ($trouble) {
                            return $trouble->toArray();
                    });
                });
            }

            $query = TroubleTicket::with(['reporter','reform']);

            if (isset($filters['subject'])) {
                $query->where('subject', $filters['subject']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            $troubles= $query->paginate($filters['per_page'] ?? 15);
            return $troubles;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single trouble with its relationships.
     *
     * @param  TroubleTicket $trouble The trouble ticket instance to retrieve.
     *
     * @return TroubleTicket $trouble
     */
    public function showTrouble(TroubleTicket $trouble)
    {
        try{
            return $trouble->load([
                    'reporter',
                    'reform'
                ]);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Create a new trouble ticket within a database transaction.
     * Automatically assigns the authenticated user as the reporter.
     * Sets the ticket status and subject based on the user's role and ticket type:
     * - If the user is an Affected Community Member:
     *     - Status is set to 'new'.
     *     - If type is 'complaint', subject is set to 'other'.
     * - If the user is a Field Monitoring Agent:
     *     - Status is set to 'waiting_assignment'.
     *     - Type is forced to 'trouble'.
     *
     * If a location is provided, it is converted to a spatial Point object.
     * Clears cached trouble tickets for all supported locales.
     *
     * @param array $data Associative array of trouble ticket attributes.
     *
     * @return TroubleTicket $trouble
     */
    public function createTrouble(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $data['user_id']= Auth::user()->id;

                //We check the role, if it is a Affected Community Member, the default status is new, and if the report is a complaint,
                // we put the subject is other, meaning it is not a malfunction.
                if(Auth::user()->hasRole('Affected Community Member')){
                    $data['status'] = 'new';
                    if($data['type'] === 'complaint'){
                        $data['subject'] = 'other';
                    }
                }
                //If the report is from the field team, the default status is waiting_assignment
                //This means that this is definitely a trouble ticket and it is waiting for a reform to be set
                elseif(Auth::user()->hasRole('Field Monitoring Agent')){
                    $data['status']= 'waiting_assignment';
                    $data['type']= "trouble";
                }

                if (isset($data['location']) && is_array($data['location'])) {
                    $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
                }

                $trouble = TroubleTicket::create($data);
                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_troubles_{$locale}");
                }
                return $trouble;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified trouble in the database.
     * Field Monitoring Agents are only allowed to update tickets with status 'waiting_assignment'.
     * The 'status' field is always ignored and cannot be updated via this method.
     * Clears cached trouble ticket data for all supported locales after update.
     *
     * @param array $data The data to update the trouble ticket with.
     * @param TroubleTicket $trouble The trouble ticket instance to be updated.
     *
     * @return TroubleTicket $trouble
     */
    public function updateTrouble(array $data, TroubleTicket $trouble)
    {
        try{
            if(Auth::user()->hasRole('Field Monitoring Agent') && $trouble->status != 'waiting_assignment')
            {
                return $this->error("An error occurred",500, 'Unfortunately, you cannot update this troubleticket after it has been processed.');
            }

            unset($data['status']);

            $trouble->update(array_filter($data));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified trouble from the database.
     *
     * @param TroubleTicket $trouble The trouble instance to be deleted.
     *
     * @return bool
     */
    public function deleteTrouble(TroubleTicket $trouble)
    {
        try{
            $deletedTrouble= $trouble->delete();
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $deletedTrouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Change the status of the trouble by the distribution and maintenance
     * network manager and discuss the possibility of change.
     *
     * Prevents changing the status to 'assigned', 'in_progress', or 'fixed'
     * if the trouble ticket has no assigned reform team.
     * Disallows status changes for reports of type 'complaint'.
     * Clears cached trouble ticket data for all supported locales after a successful update.
     *
     * @param array $data The data containing the new status
     * @param TroubleTicket $trouble The trouble ticket instance to be updated.
     *
     * @return TroubleTicket $trouble
     */
    public function changeStatus(array $data,TroubleTicket $trouble){
        try{
            //Check the status you want to change to. If it is one of these statuses,
            // it is not permissible to change to it before assigning it to the reform team.
            if(($data['status']==='assigned'
                || $data['status']==='in_progress'
                || $data['status']==='fixed')
                && !$trouble->reform )
            {
                return $this->error("An error occurred",500,
                                    'You cannot change to this status before assigning a repair team to this troubleticket'
                                    );
            }
            if($trouble->type ==='complaint'){
                return $this->error("An error occurred",500,'You cannot change the status of this report because it is a complaint. Please review it');
            }
            $trouble->update(array_filter($data));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get all trouble-type tickets reported by citizens.
     * Filters trouble tickets by type is 'trouble' and ensures the reporter
     * has the 'Affected Community Member' role. Eager loads the 'reporter' relation.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCitizenTroubles(){
        try{
            $troubles = TroubleTicket::where('type','trouble')
            ->whereHas('reporter',function($q)
                {
                    $q->role('Affected Community Member');
                })
            ->with('reporter')->paginate(15);
            return $troubles;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get all complaint-type trouble tickets reported by citizens.
     * Filters trouble tickets by type is 'Complaint' and eager loads the 'reporter' relationship.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCitizenComplaints(){
        try{
            $troubles = TroubleTicket::where('type','Complaint')
                                        ->with('reporter')->paginate(15);
            return $troubles;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Approves a trouble-type trouble ticket by updating its status to 'waiting_assignment'.
     * This method should be used only for tickets where the type is 'trouble'.
     * It is typically called by technicians or supervisors to confirm a reported
     * technical issue and initiate the assignment process.
     *
     * @param TroubleTicket $trouble The trouble ticket instance to be approved.
     *
     * @return TroubleTicket $trouble
     */
    public function approveTrouble(TroubleTicket $trouble){
        try{
            if($trouble->type === 'trouble' && $trouble->status === 'new'){
                $trouble ->update([
                    'status' => 'waiting_assignment',
                ]);
            }
            //Trigger an event to inform the reporter that the reported trouble has been approved and a reform will be assigned to it
            event(new CitizenReportOfTroubleAccepted($trouble));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Review a service-type complaint by updating its status to 'reviewed'.
     * This method should be used only for tickets where the type is 'complaint'.
     * It is typically called by support or administrative staff to close non-technical
     * issues after review or resolution.
     *
     * @param TroubleTicket $trouble The trouble ticket instance to be reviewed.
     *
     * @return TroubleTicket $trouble
     */
    public function reviewComplaint(TroubleTicket $trouble){
        try{
            if(!($trouble->type === 'complaint' && $trouble->status === 'new'))
                throw new LogicException('Only complaints can be reviewed');
                $trouble ->update([
                    'status' => 'reviewed',
                ]);
            //Trigger an event to inform the reporter that his complaint has been reviewed
            event(new ComplaintReviewed($trouble));

            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Rejects a trouble ticket if its current status allows rejection.
     *
     * This method is intended to be used when a ticket is being evaluated and the reviewer
     * decides it should be rejected. Only tickets with status 'new'
     * can be rejected. If the status is not allowed, an Exception will be thrown.
     *
     * @param TroubleTicket $trouble The trouble ticket instance to be rejected.
     *
     * @return TroubleTicket $trouble
     */
    public function rejectTrouble(TroubleTicket $trouble){
        try{
            if($trouble->status !== 'new'){
                throw new \Exception('Only trouble tickets reported by citizens can be rejected');
            }
            if($trouble->type !== 'trouble'){
                throw new \Exception('Rejection is not allowed for citizen complaints.
                                    Only citizen-submitted trouble reports are eligible for rejection.');
            }

            $trouble ->update([
                'status' => 'rejected',
            ]);
            //// Trigger an event to inform the reporter that the trouble he reported has been reviewed and no trouble as reported has been confirmed.
            event(new TroubleRejected($trouble));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_troubles_{$locale}");
            }
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

}
