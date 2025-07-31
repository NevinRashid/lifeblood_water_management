<?php

namespace Modules\DistributionNetwork\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\Pipe;

class PipeService
{
    use HandleServiceErrors;
    /**
     * Get all pipes from database
     *
     * @return array $arraydata
     */
    public function getAllPipes(array $filters = [], int $perPage = 10)
    {
        try{
            $query = Pipe::with('network');

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['distribution_network_id'])) {
                $query->where('distribution_network_id', $filters['distribution_network_id']);
            }

            $pipes = Cache::remember('all_pipes', 3600, function() use($query, $perPage){
                    $pipes= $query->paginate($perPage);
                    $pipes = $pipes->map(function($pipe){
                        $network = [
                            'id'      =>$pipe->network->id,
                            'name'    =>$pipe->network->name,
                            'address' =>$pipe->network->address,
                            'zone'    =>$pipe->network->zone->toJson(),
                        ];

                        return[
                            'id'                        => $pipe->id,
                            'name'                      => $pipe->name,
                            'status'                    => $pipe->status,
                            'distribution_network_id'   => $pipe->distribution_network_id,
                            'current_pressure'          => $pipe->current_pressure,
                            'current_flow'              => $pipe->current_flow,
                            'path'                      => $pipe->path,
                            'network'                   => $network,
                            'created_at'                => $pipe->created_at,
                            'updated_at'                => $pipe->updated_at,
                        ];
                    });
                    return $pipes;
                });
            return $pipes;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single pipe with its relationships.
     *
     * @param  Pipe $pipe
     *
     * @return Pipe $pipe
     */
    public function showPipe(Pipe $pipe)
    {
        try{
            return $pipe->load([
                    'network',
                ]);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Add new pipe to the database.
     *
     * @param array $arraydata
     *
     * @return Pipe $pipe
     */
    public function createPipe(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $points = collect($data['path'])
                    ->map(fn($coord) => new Point($coord['lat'], $coord['lng']));

                $data['path'] = new LineString($points);
                $pipe = Pipe::create($data);
                Cache::forget("all_pipes");
                return $pipe;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified pipe in the database.
     *
     * @param array $arraydata
     * @param Pipe $pipe
     *
     * @return Pipe $pipe
     */

    public function updatePipe(array $data, Pipe $pipe){
        try{
            $pipe->update(array_filter($data));
            Cache::forget("all_pipes");
            return $pipe;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified pipe from the database.
     *
     * @param Pipe $pipe
     *
     */

    public function deletePipe(Pipe $pipe){
        try{
            return DB::transaction(function () use ($pipe) {
                Cache::forget("all_pipes");
                return $pipe->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
