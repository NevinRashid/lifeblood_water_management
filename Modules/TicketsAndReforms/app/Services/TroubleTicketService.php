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
use Modules\TicketsAndReforms\Events\NewTroubleTicketCreated;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleTicketService
{
    use HandleServiceErrors;

    /**
     * Get all troubles from database
     *
     * @param array $filters
     * @param int $perPage
     *
     * @return array $arraydata
     */
    public function getAllTroubles(array $filters = [], int $perPage = 10)
    {
        try{
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

            $troubles = Cache::remember('all_troubles', 3600, function() use($query, $perPage){
                    $troubles= $query->paginate($perPage);
                    $troubles = $troubles->map(function($trouble){
                        return[
                            'id'        => $trouble->id,
                            'subject'   => $trouble->subject,
                            'status'    => $trouble->status,
                            'body'      => $trouble->body,
                            'user_id'   => $trouble->user_id,
                            'location'  => $trouble->location?->toJson(),
                        ];
                    });
                    return $troubles;
                });
            return $troubles;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single trouble with its relationships.
     *
     * @param  TroubleTicket $trouble
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
     * Add new trouble to the database.
     *
     * @param array $arraydata
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
                Cache::forget("all_troubles");
                return $trouble;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified trouble in the database.
     *
     * @param array $arraydata
     * @param TroubleTicket $trouble
     *
     * @return TroubleTicket $trouble
     */

    public function updateTrouble(array $data, TroubleTicket $trouble){
        try{

            if($trouble->status != 'new'){
                return $this->error("An error occurred",500, 'Unfortunately, you cannot edit this troubleticket. It is too late...');
            }

            //Check if the checked user who submitted the report sent a value to status,
            // then do not modify it because he is not allowed to modify this field and we will take the old value.
            if(!empty($data['status']) && Auth::user()->id === $trouble->user_id)
            {
                $data['status'] =$trouble->status;
            }
            $trouble->update(array_filter($data));
            Cache::forget("all_troubles");
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified trouble from the database.
     *
     * @param TroubleTicket $trouble
     *
     */
    public function deleteTrouble(TroubleTicket $trouble){
        try{
            return DB::transaction(function () use ($trouble) {
                Cache::forget("all_troubles");
                return $trouble->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Change the status of the trouble by the distribution and maintenance
     * network manager and discuss the possibility of change.
     *
     * @param array $arraydata
     * @param TroubleTicket $trouble
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
            $trouble->update(array_filter($data));
            Cache::forget("all_troubles");
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get all troubles reported by citizens
     *
     * @return array $arraydata
     */
    public function getAllCitizenTroubles(){
        try{
            $troubles = TroubleTicket::where('type','trouble')
            ->whereHas('reporter',function($q){
                $q->role('Affected Community Member');
            })->with('reporter')->get();
            return $troubles;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get all troubles reported by citizens
     *
     * @return array $arraydata
     */
    public function getAllCitizenComplaints(){
        try{
            $troubles = TroubleTicket::where('type','Complaint')
                                        ->with('reporter')->get();
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
     * @param TroubleTicket $trouble
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
            event(new CitizenReportOfTroubleAccepted($trouble));
            Cache::forget("all_troubles");
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
     * @param TroubleTicket $trouble
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

            Cache::forget("all_troubles");
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
     * @param TroubleTicket $trouble
     *
     * @return TroubleTicket $trouble
     */
    public function rejectTrouble(TroubleTicket $trouble){
        try{
            if($trouble->status !== 'new'){
                throw new \Exception('Only trouble tickets reported by citizens can be rejected');
            }
            $trouble ->update([
                'status' => 'rejected',
            ]);
            Cache::forget("all_troubles");
            return $trouble;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

}
