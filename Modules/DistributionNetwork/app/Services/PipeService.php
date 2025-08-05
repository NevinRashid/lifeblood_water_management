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
    public function getAllPipes(array $filters = [])
    {
        try{
            if (!$filters) {
                return Cache::remember('all_pipes_'. app()->getLocale(), now()->addDay(), function(){
                    $pipes= Pipe::with('network')->paginate(15);
                    return $pipes->through(function ($pipe) {
                            return $pipe->toArray();
                    });
                });
            }

            $query = Pipe::with('network');

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['distribution_network_id'])) {
                $query->where('distribution_network_id', $filters['distribution_network_id']);
            }

            $pipes= $query->paginate($filters['per_page'] ?? 15);
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

                foreach (config('translatable.locales') as $locale) {
                    Cache::forget("all_pipes_{$locale}");
                }
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

    public function updatePipe(array $data, Pipe $pipe)
    {
        try{
            $pipe->update(array_filter($data));

            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_pipes_{$locale}");
            }
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
    public function deletePipe(Pipe $pipe)
    {
        try{
            $deletedPipe= $pipe->delete();
            foreach (config('translatable.locales') as $locale) {
                Cache::forget("all_pipes_{$locale}");
            }
            return $deletedPipe;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
