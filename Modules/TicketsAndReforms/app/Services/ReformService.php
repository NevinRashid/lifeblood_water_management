<?php

namespace Modules\TicketsAndReforms\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToCompleted;
use Modules\TicketsAndReforms\Events\ReformStatusChangedToInProgress;
use Modules\TicketsAndReforms\Models\Reform;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReformService
{
    use HandleServiceErrors;

    //Maximum number of images in each coolection
    const MAX_IMAGES = 5;

    /**
     * Get all reforms from database
     *
     * @return array $arraydata
     */
    public function getAllReforms(array $filters = [], int $perPage = 10)
    {
        try{
            $query = Reform::with(['ticket','team']);

            if (isset($filters['team_id'])) {
                $query->where('team_id', $filters['team_id']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            $reforms = Cache::remember('all_reforms', 3600, function() use($query, $perPage){
                    $reforms= $query->paginate($perPage);
                    $reforms= $reforms->map(function($reform){
                        $trouble_ticket = [
                            'id'        =>$reform->ticket->id,
                            'subject'   =>$reform->ticket->subject,
                            'body'      =>$reform->ticket->body,
                            'status'    =>$reform->ticket->status,
                            'user_id'   =>$reform->ticket->user_id,
                            'location'  =>$reform->ticket->location->toJson(),
                        ];

                        return[
                            'id'                 => $reform->id,
                            'trouble_ticket_id'  => $reform->trouble_ticket_id,
                            'description'        => $reform->description,
                            'status'             => $reform->status,
                            'team_id'            => $reform->team_id,
                            'reform_cost'        => $reform->reform_cost,
                            'materials_used'     => $reform->materials_used,
                            'start_date'         => $reform->start_date,
                            'end_date'           => $reform->end_date,
                            'created_at'         => $reform->created_at,
                            'updated_at'         => $reform->updated_at,
                            'trouble_ticket'     => $trouble_ticket,
                        ];
                    });
                    return $reforms;
                });
            return $reforms;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single reform with its relationships.
     *
     * @param Reform $reforms
     *
     * @return Reform $reforms
     */
    public function showReform(Reform $reform)
    {
        try{
            return $reform->load([
                    'team',
                    'ticket'
                ]);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Add new reform to the database.
     *
     * @param array $arraydata
     *
     * @return Reform $reform
     */
    public function createReform(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $data['status']= 'pending';
                //TroubleTicket::whereIn('id',($data['trouble_ticket_id']))->update(['status'=>'assigned']);
                $reform = Reform::create($data);
                Cache::forget("all_reforms");
                return $reform;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified reform in the database.
     *
     * @param array $arraydata
     * @param Reform $reform
     *
     * @return Reform $reform
     */
    public function updateReform(array $data, Reform $reform)
    {
        try{
            //Check reform status if is not "Pending"
            //If there is an attempt to change the assigned team
            //or the troubleTicket to which this refprm is related, we will prevent this.
            if($reform->status != 'pending'){

                if(isset($data['team_id'])){
                    throw new HttpException(409, 'You cannot edit the team assigned this reform because the status of the reform is '.$reform->status);
                }
                elseif(isset($data['trouble_ticket_id'])){
                    throw new HttpException(409, 'You cannot edit the troubleTicket for this reform because the status of the reform is '.$reform->status);
                }
            }
            /*if($data['status']=== 'in_progress')
            event(new ReformStatusChangedToInProgress($reform));

            if($data['status']=== 'completed')
            event(new ReformStatusChangedToCompleted($reform));*/

            $reform->update(array_filter($data));
            Cache::forget("all_reforms");
            return $reform;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified reform from the database.
     *
     * @param Reform $reform
     *
     */
    public function deleteReform(Reform $reform){
        try{
            return DB::transaction(function () use ($reform) {
                Cache::forget("all_reforms");
                return $reform->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     *
     */
    public function addReformImages(array $data,Reform $reform)
    {
        try{
            $beforeImages = $data['before_images'] ?? [];
            $afterImages  = $data['after_images'] ?? [];

            $this->uploadImagesToCollection($reform, $beforeImages, 'before_repair');
            $this->uploadImagesToCollection($reform, $afterImages, 'after_repair');

            return [
                'before_images' => $reform->getMedia('before_repair')->map->getUrl(),
                'after_images'  => $reform->getMedia('after_repair')->map->getUrl(),
            ];

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * This function uploads the set of images to the appropriate collection using the Spatie Media package.
     *
     * @param Reform $reform
     * @param array $images
     * @param string $collection
     */
    protected function uploadImagesToCollection(Reform $reform, array $images, string $collection)
    {
        try{
            if (empty($images)) return;

            $existingCount = $reform->getMedia($collection)->count();
            $incomingCount = count($images);

            if ($existingCount + $incomingCount > self::MAX_IMAGES) {
                throw new \Exception("The maximum number of images in a collection ($collection) is" . self::MAX_IMAGES);
            }

            foreach ($images as $image) {
                $reform->addMedia($image)->toMediaCollection($collection);
            }

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
