<?php

namespace App\Services\Base;

use App\Exceptions\CrudException;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Services\Interfaces\BaseServiceInterface;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Abstract base class for services, providing common business logic operations
 * and centralized exception handling.
 *
 * This class implements the BaseServiceInterface and offers generic methods
 * for interacting with repositories, such as retrieving all records,
 * fetching a single record, storing, updating, and deleting data.
 * It also includes a `handle` method to wrap operations in a try-catch block
 * for consistent error management.
 *
 * @package App\Services\Base
 */
abstract class BaseService implements BaseServiceInterface
{

    /**
     * The Base repository instance
     *
     * @var BaseRepositoryInterface
     */
    protected BaseRepositoryInterface $repository;


    /**
     * Handles the execution of a given callback, providing centralized exception handling.
     *
     * Catches common exceptions like ModelNotFoundException,
     * and general Throwable instances, re-throwing them as custom CrudException or the original exception.
     *
     * @param Closure $callback The callback function to execute.
     * @return mixed The result of the executed callback.
     * @throws CrudException If a resource is not found (404) or an unexpected error occurs (500).
     * @throws \Throwable For any other unhandled exceptions.
     */
    protected function handle(Closure $callback)
    {
        try {
            return $callback();
        } catch (ModelNotFoundException $e) {
            throw new CrudException("Resource Not Found", 404);
        } catch (\Throwable $e) {
            throw new CrudException('An unexpected error,', 500);
        }
    }

    /**
     * Retrieve all records from the associated repository, with optional filtering and pagination.
     *
     * @param array $filters An associative array of filters, which may include pagination parameters.
     * @return LengthAwarePaginator The paginated list of model instances.
     */
    public function getAll(array $filters = [])
    {
        return $this->handle(function () use ($filters) {

            return $this->repository->getAll($filters);
        });
    }


    /**
     * Retrieve a single model instance by its ID or return the provided model instance.
     *
     * @param string|Model $modelOrId The ID of the record to retrieve, or an existing Model instance.
     * @return Model The retrieved model instance.
     * @throws CrudException If the resource is not found (404).
     */
    public function get(string|Model $modelOrId)
    {
        return $this->handle(function () use ($modelOrId) {
            return $this->repository->get($modelOrId);
        });
    }

    /**
     * Store a new record in the database via the associated repository.
     *
     * @param array $data The data to be stored for the new record.
     * @return Model The newly created model instance.
     * @throws CrudException If an unexpected error occurs (500).
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            return $this->repository->store($data);
        });
    }

    /**
     * Update an existing record in the database via the associated repository.
     *
     * @param array $data The data to update the record with.
     * @param string|Model $modelOrId The ID of the record to update, or an existing Model instance.
     * @return Model The updated model instance.
     * @throws CrudException If the resource is not found (404) or an unexpected error occurs (500).
     */
    public function update(array $data, string|Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {

            return $this->repository->update($data, $modelOrId);
        });
    }

    /**
     * Delete the specified model from the database via the associated repository.
     *
     * @param string|Model $modelOrId The ID of the record to delete, or an existing Model instance.
     * @return bool|null True if the model was deleted, false otherwise. Null if no model found.
     * @throws CrudException If the resource is not found (404) or an unexpected error occurs (500).
     */
    public function destroy(string|Model $modelOrId)
    {
        return $this->handle(function () use ($modelOrId) {
            return $this->repository->destroy($modelOrId);
        });
    }
}
