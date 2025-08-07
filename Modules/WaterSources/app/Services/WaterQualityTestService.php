<?php

namespace Modules\WaterSources\Services;

use Exception;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Modules\WaterSources\Models\WaterSource;
use Modules\WaterSources\Models\WaterQualityTest;
use Modules\WaterSources\Jobs\SendDetailedReportJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WaterQualityTestService extends BaseService
{



    /**
     * Get a paginated list of water quality tests with filtering and caching.
     *
     * @param array $filters Filters for the query ( 'date_from', 'date_to'...).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        return $this->handle(function () use ($filters) {
            $cacheKey = 'water_quality_tests_' . md5(json_encode($filters));

            return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters) {
                $query = WaterQualityTest::query();

                if (!empty($filters['date_from'])) {
                    $query->whereDate('test_date', '>=', $filters['date_from']);
                }

                if (!empty($filters['date_to'])) {
                    $query->whereDate('test_date', '<=', $filters['date_to']);
                }

                return $query->latest('test_date')->paginate(10);
            });
        });
    }

    /**
     * Get a single water quality test by its ID, with caching.
     *
     * @param int $id The ID of the test.
     * @return \Modules\WaterSources\Models\WaterQualityTest
     */
    public function show(int $id): WaterQualityTest
    {
        return $this->handle(function () use ($id) {
            return Cache::remember("water_quality_test_{$id}", now()->addMinutes(10), function () use ($id) {
                return WaterQualityTest::findOrFail($id);
            });
        });
    }

    /**
     * Store a new test, evaluate it against standards, and return the result.
     *
     * @param array $data The data for the new test.
     * @return array{'test': WaterQualityTest, 'failed_parameters': array} An array containing the created test and any failed parameters.
     */
    public function store(array $data): array
    {
        return $this->handle(function () use ($data) {
            $waterSource = WaterSource::with('parameters')->findOrFail($data['water_source_id']);
            $evaluationResult = $this->evaluateParameters($data, $waterSource->parameters);
            $data['meets_standard_parameters'] = $evaluationResult['meets_standards'];

            $test = WaterQualityTest::create($data);
            Cache::forget('water_quality_tests_' . md5(json_encode([])));
            return [
                'test' => $test,
                'failed_parameters' => $evaluationResult['failed_parameters']
            ];
        });
    }

    /**
     * Update an existing test, re-evaluate it, and return the result.
     *
     * @param int $id The ID of the test to update.
     * @param array $data The new data for the test.
     * @return array{'test': WaterQualityTest, 'failed_parameters': array} An array containing the updated test and any failed parameters.
     */
    public function update( array $data , $id): array
    {
        return $this->handle(function () use ($id, $data) {
            $test = WaterQualityTest::findOrFail($id);
            $updatedDataForEvaluation = array_merge($test->toArray(), $data);

            $waterSource = WaterSource::with('parameters')->findOrFail($updatedDataForEvaluation['water_source_id']);
            $evaluationResult = $this->evaluateParameters($updatedDataForEvaluation, $waterSource->parameters);
            $data['meets_standard_parameters'] = $evaluationResult['meets_standards'];

            $test->update($data);
            Cache::forget("water_quality_test_{$id}");
            Cache::forget('water_quality_tests_' . md5(json_encode([])));

            return [
                'test' => $test->fresh(), // Use fresh() to get the updated model
                'failed_parameters' => $evaluationResult['failed_parameters'],
            ];
        });
    }

    /**
     * Delete a water quality test and clear relevant cache entries.
     *
     * @param int $id The ID of the test to delete.
     * @return bool
     */
    public function destroy( $id): bool
    {
        return $this->handle(function () use ($id) {
            $test = WaterQualityTest::findOrFail($id);
            $test->delete();

            Cache::forget("water_quality_test_{$id}");
            Cache::forget('water_quality_tests_' . md5(json_encode([])));

            return true;
        });
    }

    /**
     * Get a detailed report for a specific test and dispatch an email job.
     *
     * @param int $id The ID of the WaterQualityTest.
     * @return \Modules\WaterSources\Models\WaterQualityTest
     */
    public function getReportAndDispatchEmail(int $id): WaterQualityTest
    {
        return $this->handle(function () use ($id) {
            $test = WaterQualityTest::with([
                'waterSource.parameters'
            ])->findOrFail($id);
            if (Auth::check()) {
                SendDetailedReportJob::dispatch($test,  Auth::user());
            }
            return $test;
        });
    }

    /**
     * Evaluates test data against a collection of standards.
     * This is a private helper method to avoid code duplication.
     *
     * @param array $testData The data from the test (e.g., ['ph_level' => 7.5, ...]).
     * @param \Illuminate\Database\Eloquent\Collection $standards The collection of TestingParameter models to check against.
     * @return array An associative array with 'meets_standards' (boolean) and 'failed_parameters' (array).
     */
    private function evaluateParameters(array $testData, Collection $standards): array
    {
        $meetsStandards = true;
        $failedParametersDetails = [];

        foreach ($standards as $standard) {
            $columnName = $standard->name;

            if (!isset($testData[$columnName])) {
                continue;
            }

            $testValue = $testData[$columnName];
            $isFailed = false;

            if ($standard->minimum_level !== null && $testValue < $standard->minimum_level) {
                $isFailed = true;
            }

            if (!$isFailed && $standard->maximum_level !== null && $testValue > $standard->maximum_level) {
                $isFailed = true;
            }

            if ($isFailed) {
                $meetsStandards = false;
                $failedParametersDetails[] = [
                    'parameter' => $columnName,
                    'value_recorded' => $testValue,
                    'minimum_allowed' => $standard->minimum_level,
                    'maximum_allowed' => $standard->maximum_level,
                ];
            }
        }

        return [
            'meets_standards' => $meetsStandards,
            'failed_parameters' => $failedParametersDetails,
        ];
    }
}
