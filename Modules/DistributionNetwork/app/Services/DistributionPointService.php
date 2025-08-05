<?php

namespace Modules\DistributionNetwork\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\DistributionPoint;

class DistributionPointService
{
    use HandleServiceErrors;
    /**
     * Get all distributionPoints from database
     *
     * @return array $arraydata
     */
    public function getAllPoints(array $filters = [])
    {
        try{
            if (!$filters) {
                return Cache::remember('all_points_'. app()->getLocale(), now()->addDay(), function(){
                    $points= DistributionPoint::with('network')->paginate(15);
                    return $points->through(function ($point) {
                            return $point->toArray();
                    });
                });
            }

            $query = DistributionPoint::with('network');

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (isset($filters['distribution_network_id'])) {
                $query->where('distribution_network_id', $filters['distribution_network_id']);
            }
            $points= $query->paginate($filters['per_page'] ?? 15);
            return $points;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single distributionPoint with its relationships.
     *
     * @param  DistributionPoint $point
     *
     * @return DistributionPoint $point
     */
    public function showPoint(DistributionPoint $point)
    {
        try{
            return $point->load([
                    'network',
                    'deliveries'
                ])->loadCount('deliveries');

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Add new distributionPoint to the database.
     *
     * @param array $arraydata
     *
     * @return DistributionPoint $point
     */
    public function createPoint(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                if (isset($data['location']) && is_array($data['location'])) {
                    $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
                }
                $point = DistributionPoint::create($data);
                
                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_points_{$locale}");
                }
                return $point;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified distributionPoint in the database.
     *
     * @param array $arraydata
     * @param DistributionPoint $point
     *
     * @return DistributionPoint $point
     */

    public function updatePoint(array $data, DistributionPoint $point){
        try{
            $point->update(array_filter($data));
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_points_{$locale}");
            }
            return $point;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified distributionPoint from the database.
     *
     * @param DistributionPoint $point
     *
     */
    public function deletePoint(DistributionPoint $point){
        try{
            return DB::transaction(function () use ($point) {
                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_points_{$locale}");
                }
                return $point->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
