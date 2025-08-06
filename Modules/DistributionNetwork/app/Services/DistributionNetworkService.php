<?php

namespace Modules\DistributionNetwork\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Illuminate\Support\Facades\DB;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class DistributionNetworkService
{
    use HandleServiceErrors;
    /**
     * Get all networks from database
     *
     * @return array $arraydata
     */
    public function getAllNetworks(array $filters = [])
    {
        try{
            if (!$filters) {
                return Cache::remember('all_networks_'. app()->getLocale(), now()->addDay(), function(){
                    $networks= DistributionNetwork::with([
                                'reservoirs','distributionPoints',
                                'pumpingStations','valves','pipes'
                                ])->paginate(10);
                        return $networks->through(function ($network) {
                                return $network->toArray();
                        });
                });
            }
            $query = DistributionNetwork::with(['reservoirs','distributionPoints','pumpingStations','valves','pipes']);

            if (isset($filters['name'])) {
                $query->where('name', $filters['name']);
            }

            if (isset($filters['water_source_id'])) {
                $query->where('water_source_id', $filters['water_source_id']);
            }

            if (isset($filters['manager_id'])) {
                $query->where('manager_id', $filters['manager_id']);
            }
            $networks= $query->paginate($filters['per_page'] ?? 15);
            return $networks;

        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
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
        try {
            return $network->load([
                'reservoirs',
                'distributionPoints',
                'pumpingStations',
                'valves',
                'pipes',
            ])->loadCount([
                'reservoirs',
                'distributionPoints',
                'pumpingStations',
                'valves',
                'pipes',
            ]);
        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
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
        try {
            return DB::transaction(function () use ($data) {

                $points = collect($data['zone'])
                    ->map(fn($coord) => new Point($coord['lat'], $coord['lng']));

                $linestring = new LineString($points);
                $data['zone'] = new Polygon([$linestring]);

                $network = DistributionNetwork::create($data);

                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_networks_{$locale}");
                }
                return $network;
            });
        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
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

    public function updateNetwork(array $data, DistributionNetwork $network)
    {
        try {
            $network->update(array_filter($data));

            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_networks_{$locale}");
            }

            return $network;
        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
        }
    }

    /**
     * Delete the specified network from the database.
     *
     * @param DistributionNetwork $network
     *
     */
    public function deleteNetwork(DistributionNetwork $network)
    {
        try {
            return DB::transaction(function () use ($network) {
                $network->reservoirs()->delete();
                $network->distributionPoints()->delete();
                $network->pumpingStations()->delete();
                $network->valves()->delete();
                $network->pipes()->delete();

                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_networks_{$locale}");
                }
                return $network->delete();
            });
        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
        }
    }

    /**
     *
     */
    public function updateCurrentVolume(array $data, DistributionNetwork $network)
    {
        try {
            DB::beginTransaction();

            $network->update([
                'current_volume' => $data['extracted'],
            ]);

            DB::commit();

            return $network ;

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('network not found', 404);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->error("An error occurred", 500, $th->getMessage());
        }
    }

    /**
     * Retrieve all trouble tickets with their network.
     */
    public function review()
    {
        try {
            // Fetch all tickets and transform into simplified array
            $tickets = TroubleTicket::all()->load(['reporter', 'reform'])->map(function ($ticket) {
                return [
                    'id'       => $ticket->id,
                    'subject'  => $ticket->subject,
                    'status'   => $ticket->status,
                    'type'     => $ticket->type,
                    'body'     => $ticket->body,
                    'reporter' => $ticket->reporter->name,
                    'network'  => $ticket->network, // the distribution name
                    'reform' => [
                        'info' => $ticket->reform,
                    ]
                ];
            });
            return $tickets;
        } catch (\Throwable $th) {
            return $this->error("An error occurred", 500, $th->getMessage());
        }
    }
}
