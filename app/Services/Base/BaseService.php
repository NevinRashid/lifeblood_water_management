<?php

namespace App\Services\Base;

use App\Exceptions\CrudException;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseService
{

    protected Model $model;

    protected function handle(Closure $callback)
    {
        try {
        return $callback();
        } catch (ModelNotFoundException $e) {
            throw new CrudException("Resource Not Found", 404);
        } catch (\Throwable $e) {

            if (config('app.debug', false)) {

                $detailedMessage = sprintf(
                    "Error: %s in %s on line %d",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),

                );
                throw new CrudException($detailedMessage, 500);
            }

            throw new CrudException('An unexpected error has occurred.', 500);
        }
    }

    /**
     * Throws an HttpResponseException with a formatted JSON error response.
     * @param mixed $message
     * @param mixed $code
     * @param mixed $errors
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function throwExceptionJson($message = 'An error occurred', $code = 500, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        throw new HttpResponseException(response()->json($response, $code));
    }

    /**
     * Get paginated locations
     *
     * @return LengthAwarePaginator The paginated list of  model
     */
    public function getAll(array $filters = [])
    {
        return $this->handle(function () use ($filters) {

            $query = $this->query($filters);
            $perPage = $filters['per_page'] ?? 10;
            return $query->paginate($perPage);
        });
    }


    /**
     * to get one model using id
     *
     * @param string get model by id
     */
    public function get(string $id)
    {
        return $this->handle(function () use ($id) {
            return $this->model->findOrFail($id);
        });
    }

    /**
     * For store a new model
     *
     * @param array $data To store the model
     */
    public function store(array $data)
    {
        return $this->handle(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * For update a model
     *
     * @param array $data To Update the model
     * @param string|Model $id get model by id
     */
    public function update(array $data, string|Model $modelOrId)
    {
        return $this->handle(function () use ($data, $modelOrId) {

            if (!($modelOrId instanceof Model)) {
                $model = $this->model->findOrFail($modelOrId);
                $model->update($data);
                return $model;
            }

            $modelOrId->update($data);
            return $modelOrId;
        });
    }

    /**
     *  Delete the specified model
     *
     *  @param string $id get model by id
     *  @return bool|null True if the model was deleted, false otherwise
     */
    public function destroy(string $id)
    {
        return $this->handle(function () use ($id) {
            return $this->model->findOrFail($id)->delete();
        });
    }

    /**
     * This method prepare a query from the model
     * and allow to override the `getAll` query
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder<Model>
     */
    protected function query(array $filters = [])
    {
        $query = $this->model->newQuery();
        return $query;
    }
}
