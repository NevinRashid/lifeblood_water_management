<?php

namespace Modules\TicketsAndReforms\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\TicketsAndReforms\Models\Reform;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReformService
{
    use HandleServiceErrors;

    //Maximum number of images in each coolection
    const MAX_IMAGES = 5;

    /**
     * Get all reforms from database.
     * If no filters are provided, results are cached per locale for one day.
     * Supports filtering by team ID and status.
     *
     * @param array $filters
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllReforms(array $filters = [])
    {
        try{
            if (!$filters) {
                return Cache::remember('all_reforms_'. app()->getLocale(), now()->addDay(), function(){
                    $reforms= Reform::with(['ticket','team'])->paginate(15);
                    return $reforms->through(function ($reform) {
                            return $reform->toArray();
                    });
                });
            }
            $query = Reform::with(['ticket','team']);

            if (isset($filters['team_id'])) {
                $query->where('team_id', $filters['team_id']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            $reforms= $query->paginate($filters['per_page'] ?? 15);
            return $reforms;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single reform with its relationships.
     * and fetch associated image URLs.
     *
     * @param Reform $reforms The reform instance to retrieve.
     *
     * @return array Returns an array containing the reform and its images.
     */
    public function showReform(Reform $reform)
    {
        try{
            $reform = $reform->load([
                        'team',
                        'ticket'
                    ]);
            $images = $this->getImagesUrl($reform);

            return [
                $reform,
                $images
            ];

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Create a new reform record within a database transaction.
     * Sets the initial status to 'pending',
     * and clears cached reform data for all configured locales.
     *
     * @param array $data Associative array of reform attributes.
     *
     * @return Reform $reform
     *
     */
    public function createReform(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $data['status']= 'pending';
                $reform = Reform::create($data);
                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_reforms_{$locale}");
                }
                return $reform;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified reform in the database.
     * Prevents changing the assigned team or related trouble ticket if the reform's status is not "pending".
     * Also clears the cached list of reforms for all supported locales.
     *
     * @param array $arraydata The new data to update the reform with.
     * @param Reform $reform The reform instance to be updated.
     *
     * @return Reform $reform
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * If attempting to change team or trouble ticket when reform status is not "pending".
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

            $reform->update(array_filter($data));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_reforms_{$locale}");
            }
            return $reform;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified reform from the database and clear related cache.
     *
     * @param Reform $reform  The reform instance to be deleted.
     *
     * @return bool
     */
    public function deleteReform(Reform $reform)
    {
        try{
            $deletedReform= $reform->delete();
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_reforms_{$locale}");
            }
            return $deletedReform;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Uploads before and after repair images for a given reform.
     *
     * @param array $data Includes optional 'before_images' and 'after_images'.
     * @param Reform $reform The reform to attach images to.
     *
     * @return array Image URLs grouped by type.
     *
     * @throws \Throwable On upload failure.
     */
    public function addReformImages(array $data,Reform $reform)
    {
        try{
            $beforeImages = $data['before_images'] ?? [];
            $afterImages  = $data['after_images'] ?? [];

            $this->uploadImagesToCollection($reform, $beforeImages, 'before_repair');
            $this->uploadImagesToCollection($reform, $afterImages, 'after_repair');

            return $this->getImagesUrl($reform);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * This function uploads the set of images to the appropriate collection using the Spatie Media package.
     *
     * @param Reform $reform The reform to attach images to.
     * @param array $images Array of uploaded image files.
     * @param string $collection Target media collection name.
     *
     * @throws \Exception If image limit is exceeded.
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

    /**
     * Get all images URLs related to a given reform.
     *
     * @param Reform $reform The reform instance.
     *
     * @return array URLs of 'before_repair' and 'after_repair' images.
     *
     * @throws \Throwable On failure to fetch media.
     */
    public function getImagesUrl(Reform $reform)
    {
        try{
            $before_images_url=  $reform->getMedia('before_repair')->map(function($media) { return $media->getUrl();});
            $after_images_url=  $reform->getMedia('after_repair')->map(function($media) { return $media->getUrl();});

            return [
                'before_images' => $before_images_url,
                'after_images'  => $after_images_url,
            ];

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
