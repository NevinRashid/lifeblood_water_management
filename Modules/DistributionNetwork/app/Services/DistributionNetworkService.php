<?php

namespace Modules\DistributionNetwork\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Illuminate\Support\Facades\DB;


class DistributionNetworkService
{
    use HandleServiceErrors;
    /**
     * Get all networks from database
     *
     * @return array $arraydata
     */
    public function getAllNetworks()
    {
        try{
            $networks = Cache::remember('all_networks', 3600, function(){
                    $networks= DistributionNetwork::all();
                    $networks = $networks->map(function($network){
                        return[
                            'id'      => $network->id,
                            'name'    => $network->name,
                            'address' => $network->address,
                            'mamager' => $network->manager->name ,
                            'zone'    => $network->zone?->toJson(),
                        ];
                    });
                    return $networks;
                });
            return $networks;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single networks with its relationships.
     *
     * @param  DistributionNetwork $network
     *
     * @return DistributionNetwork $network
     */
    public function showNetwork(DistributionNetwork $network)
    {
        try{
            return $network->load([
                    'reservoirs',
                    'distributionPoints',
                    'pumpingStations',
                    'valves',
                    'pipes',
                ])->loadCount(['reservoirs','distributionPoints','pumpingStations',
                                'valves','pipes',]);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Add new network to the database.
     *
     * @param array $arraydata
     *
     * @return DistributionNetwork $network
     */
    public function createNetwork(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $network = DistributionNetwork::create($data);
                Cache::forget("all_networks");
                return $network;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified network in the database.
     *
     * @param array $arraydata
     * @param DistributionNetwork $network
     *
     * @return DistributionNetwork $network
     */

    public function updateNetwork(array $data, DistributionNetwork $network){
        try{
            $network->update(array_filter($data));
            Cache::forget("all_networks");
            return $network;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified network from the database.
     *
     * @param DistributionNetwork $network
     *
     */

    public function deleteNetwork(DistributionNetwork $network){
        try{
            return DB::transaction(function () use ($network) {
                $network->reservoirs()->delete();
                $network->distributionPoints()->delete();
                $network->pumpingStations()->delete();
                $network->valves()->delete();
                $network->pipes()->delete();
                Cache::forget("all_networks");
                return $network->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

}
