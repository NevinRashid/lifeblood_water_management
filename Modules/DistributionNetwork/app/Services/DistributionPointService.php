<?php

namespace Modules\DistributionNetwork\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\DistributionNetwork\Models\DistributionPoint;

class DistributionPointService
{
    use HandleServiceErrors;
    /**
     * Get all distributionPoints from database
     *
     * @return array $arraydata
     */
    public function getAllPoints(array $filters = [], int $perPage = 10)
    {
        try{
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

            $points = Cache::remember('all_points', 3600, function() use($query, $perPage){
                    $points= $query->paginate($perPage);
                    $points = $points->map(function($point){
                        $network = [
                            'id'      =>$point->network->id,
                            'name'    =>$point->network->name,
                            'address' =>$point->network->address,
                            'zone'    =>$point->network->zone->toJson(),
                        ];

                        return[
                            'id'                        => $point->id,
                            'name'                      => $point->name,
                            'status'                    => $point->status,
                            'type'                      => $point->type,
                            'distribution_network_id'   => $point->distribution_network_id,
                            'location'                  => $point->location?->toJson(),
                            'network'                   => $network,
                        ];
                    });
                    return $points;
                });
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
                $point = DistributionPoint::create($data);
                Cache::forget("all_points");
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
            Cache::forget("all_points");
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
                Cache::forget("all_points");
                return $point->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
