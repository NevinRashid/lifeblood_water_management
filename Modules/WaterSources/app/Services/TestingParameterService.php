<?php

namespace Modules\WaterSources\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\WaterSources\Models\TestingParameter;
use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Model;

class TestingParameterService extends BaseService
{
    /**
     * The model instance.
     * @var TestingParameter
     */
    protected Model $model;

    /**
     * Constructor to set the model.
     */
    public function __construct()
    {
        $this->model = new TestingParameter();
    }

    /**
     * Get all testing parameters, with caching.
     * We override this method to keep the caching logic.
     */
    public function index()
    {
        // Use handle() for standardized error handling
        return $this->handle(function () {
            return Cache::remember('testing_parameters_all', 3600, function () {
                return $this->model->latest()->get();
            });
        });
    }

    /**
     * Get a single testing parameter by ID, with caching.
     * We override this method to keep the caching logic.
     * @param mixed $id
     */
    public function show($id)
    {
        // Use handle() for standardized error handling
        return $this->handle(function () use ($id) {
            return Cache::remember("testing_parameter_{$id}", 3600, function () use ($id) {
                return $this->model->findOrFail($id);
            });
        });
    }

    /**
     * Store a new testing parameter.
     * We override this to include DB transaction and cache clearing.
     * @param array $data
     * @return TestingParameter
     */
    public function store(array $data): TestingParameter
    {
        // Use handle() to wrap the entire operation
        return $this->handle(function () use ($data) {

            // Use DB::transaction for atomicity
            $parameter = DB::transaction(function () use ($data) {
                return $this->model->create($data);
            });

            // Clear cache after successful creation
            Cache::forget('testing_parameters_all');

            return $parameter;
        });
    }

    /**
     * Update an existing testing parameter.
     * We override this to handle cache clearing.
     * @param string|Model $modelOrId
     * @param array $data

     */
    public function update(array $data,$modelOrId )
    {
        return $this->handle(function () use ($modelOrId, $data) {
            $parameter = ($modelOrId instanceof Model) ? $modelOrId : $this->model->findOrFail($modelOrId);

            $parameter->update($data);

            // Clear relevant cache entries
            Cache::forget("testing_parameter_{$parameter->id}");
            Cache::forget('testing_parameters_all');

            return $parameter->fresh();
        });
    }

    /**
     * Delete a testing parameter.
     * We override this to handle cache clearing.
     * @param string|Model $modelOrId
     * @return bool
     */
    public function destroy($modelOrId): bool
    {
        // Use handle() to wrap the delete and cache logic
        return $this->handle(function () use ($modelOrId) {

            $parameter = ($modelOrId instanceof Model) ? $modelOrId : $this->model->findOrFail($modelOrId);

            // Get the ID before deleting to use it for the cache key
            $id = $parameter->id;

            $parameter->delete();

            // Clear relevant cache entries
            Cache::forget("testing_parameter_{$id}");
            Cache::forget('testing_parameters_all');

            return true;
        });
    }
}
