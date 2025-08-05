<?php

namespace Modules\WaterSources\Services;


use Illuminate\Database\Eloquent\Collection;
use Modules\WaterSources\Models\WaterSource;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\WaterSources\Models\TestingParameter;

class WaterSourceParameterService
{
    /**
     *
     * @param WaterSource $waterSource
     * @return LengthAwarePaginator
     */
    public function getParametersForSource(WaterSource $waterSource): LengthAwarePaginator
    {
        return $waterSource->parameters()->paginate(20);
    }

    /**
     *
     * @param WaterSource $waterSource
     * @param array $parameterIds
     * @return Collection
     */
    public function assignParameters(WaterSource $waterSource, array $parameterIds): Collection
    {
        $waterSource->parameters()->syncWithoutDetaching($parameterIds);
        //   $waterSource->load('parameters');

        return $waterSource->parameters;
    }

    /**
     *
     * @param WaterSource $waterSource
     * @param array $parameterIds
     * @return Collection
     */
    public function syncParameters(WaterSource $waterSource, array $parameterIds): Collection
    {
        $waterSource->parameters()->sync($parameterIds);

        return $waterSource->parameters;
    }

    /**
     *
     *
     * @param WaterSource $waterSource
     * @param TestingParameter $parameter
     * @return int
     */
    public function removeParameter(WaterSource $waterSource, TestingParameter $parameter): int
    {
        return $waterSource->parameters()->detach($parameter->id);
    }
}
