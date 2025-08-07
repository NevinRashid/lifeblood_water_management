<?php

namespace Modules\WaterSources\Services;

use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\WaterSources\Models\TestingParameter;
use Modules\WaterSources\Models\WaterSource;

class WaterSourceParameterService extends BaseService
{
    /**
     * WaterSourceParameterService constructor.
     * We set the base model to WaterSource as it's the main entity we operate on.
     */
    public function __construct()
    {
        $this->model = new WaterSource();
    }

    /**
     * Get a paginated list of parameters assigned to a specific water source.
     *
     * @param WaterSource $waterSource The water source to get parameters for.
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getParametersForSource(WaterSource $waterSource): LengthAwarePaginator
    {
        return $this->handle(function () use ($waterSource) {
            return $waterSource->parameters()->paginate(20);
        });
    }

    /**
     * Assign one or more parameters to a water source without removing existing ones.
     *
     * @param WaterSource $waterSource The water source to assign parameters to.
     * @param array $parameterIds An array of TestingParameter IDs.
     * @return \Illuminate\Database\Eloquent\Collection The fresh collection of all assigned parameters.
     */
    public function assignParameters(WaterSource $waterSource, array $parameterIds): Collection
    {
        return $this->handle(function () use ($waterSource, $parameterIds) {
            $waterSource->parameters()->syncWithoutDetaching($parameterIds);

            // Reload the relationship to ensure the returned collection is up-to-date.
            return $waterSource->load('parameters')->parameters;
        });
    }

    /**
     * Synchronize the parameters for a water source, removing any not in the provided list.
     *
     * @param WaterSource $waterSource The water source to sync parameters for.
     * @param array $parameterIds The complete array of TestingParameter IDs that should be assigned.
     * @return \Illuminate\Database\Eloquent\Collection The final collection of assigned parameters.
     */
    public function syncParameters(WaterSource $waterSource, array $parameterIds): Collection
    {
        return $this->handle(function () use ($waterSource, $parameterIds) {
            $waterSource->parameters()->sync($parameterIds);

            // Reload the relationship to ensure the returned collection is up-to-date.
            return $waterSource->load('parameters')->parameters;
        });
    }

    /**
     * Remove a single parameter from a water source.
     *
     * @param WaterSource $waterSource The water source to remove the parameter from.
     * @param TestingParameter $parameter The parameter to be removed.
     * @return int The number of records detached (usually 1 or 0).
     */
    public function removeParameter(WaterSource $waterSource, TestingParameter $parameter): int
    {
        return $this->handle(function () use ($waterSource, $parameter) {
            return $waterSource->parameters()->detach($parameter->id);
        });
    }
}
