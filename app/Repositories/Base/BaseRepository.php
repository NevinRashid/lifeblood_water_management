<?php

namespace App\Repositories\Base;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Abstract base class for repositories, providing common CRUD operations.
 *
 * This class implements the BaseRepositoryInterface and offers generic methods
 * for interacting with Eloquent models, such as retrieving all records,
 * fetching a single record, storing, updating, and deleting data.
 * Child repositories should extend this class and inject their specific Model.
 *
 * @package App\Repositories\Base
 */
abstract class BaseRepository implements BaseRepositoryInterface
{

    /**
     * The Eloquent model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Retrieve all records from the model, with optional filtering and pagination.
     *
     * @param array $filters An associative array of filters, including 'per_page' for pagination.
     * @return LengthAwarePaginator A paginated collection of model instances.
     */
    public function getAll(array $filters = [])
    {
        $query = $this->query($filters);
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Retrieve a single record by its ID or return the provided model instance.
     *
     * @param string|Model $modelOrId The ID of the record to retrieve, or an existing Model instance.
     * @return Model The retrieved model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model with the given ID is not found.
     */
    public function get(string|Model $modelOrId)
    {
        if (!($modelOrId instanceof Model)) {
            return $this->model->findOrFail($modelOrId);
        }

        return $modelOrId;
    }

    /**
     * Store a new record in the database.
     *
     * @param array $data The data to be stored for the new record.
     * @return Model The newly created model instance.
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record in the database.
     *
     * @param array $data The data to update the record with.
     * @param string|Model $modelOrId The ID of the record to update, or an existing Model instance.
     * @return Model The updated model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model with the given ID is not found.
     */
    public function update(array $data, string|Model $modelOrId)
    {
        if (!($modelOrId instanceof Model)) {
            $model = $this->model->findOrFail($modelOrId);
            $model->update($data);
            return $model;
        }

        $modelOrId->update($data);
        return $modelOrId;
    }

    /**
     * Delete a record from the database.
     *
     * @param string|Model $modelOrId The ID of the record to delete, or an existing Model instance.
     * @return bool|null True if the model was deleted, false otherwise. Null if no model found.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model with the given ID is not found.
     */
    public function destroy(string|Model $modelOrId)
    {
        if (!($modelOrId instanceof Model)) {
            return $this->model->findOrFail($modelOrId)->delete();
        }

        return $modelOrId->delete();
    }

    /**
     * Prepare a base query for the model.
     *
     * This method can be overridden in child repositories to apply custom default
     * filtering, eager loading, or other query modifications.
     *
     * @param array $filters An associative array of filters to apply to the query.
     * @return Builder<Model> The Eloquent query builder instance.
     */
    public function query(array $filters = [])
    {
        return $this->model->newQuery();
    }
}
