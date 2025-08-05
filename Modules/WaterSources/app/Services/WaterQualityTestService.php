<?php

namespace Modules\WaterSources\Services;

use App\Jobs\SendDetailedReportJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Events\ReportGenerationRequested;
use Illuminate\Database\Eloquent\Collection;
use Modules\WaterSources\Models\WaterSource;
use Modules\WaterSources\Events\WaterTestFailed;
use Modules\WaterSources\Models\WaterQualityTest;
use Modules\DistributionNetwork\Models\DistributionPoint;

class WaterQualityTestService
{
    public function handle() {}

    public function index(array $filters = [])
    {
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
    }

    public function show(int $id)
    {
        return Cache::remember("water_quality_test_{$id}", now()->addMinutes(10), function () use ($id) {
            return WaterQualityTest::findOrFail($id);
        });
    }
    /**
     * Store a new test and return the result.
     */
    public function store(array $data): array
    {
        $waterSource = WaterSource::with('parameters')->findOrFail($data['water_source_id']);
        $evaluationResult = $this->evaluateParameters($data, $waterSource->parameters);
        $data['meets_standard_parameters'] = $evaluationResult['meets_standards'];

        $test = WaterQualityTest::create($data);
        Cache::forget('water_quality_tests_' . md5(json_encode([])));
        return [
            'test' => $test,
            'failed_parameters' => $evaluationResult['failed_parameters']
        ];
    }

    /**
     * Update a test and return the result.
     */
    public function update(int $id, array $data): array
    {
        $test = WaterQualityTest::findOrFail($id);
        $updatedDataForEvaluation = array_merge($test->toArray(), $data);

        $waterSource = WaterSource::with('parameters')->findOrFail($updatedDataForEvaluation['water_source_id']);
        $evaluationResult = $this->evaluateParameters($updatedDataForEvaluation, $waterSource->parameters);
        $data['meets_standard_parameters'] = $evaluationResult['meets_standards'];

        $test->update($data);
        Cache::forget("water_quality_test_{$id}");
        Cache::forget('water_quality_tests_' . md5(json_encode([])));

        return [
            'test' => $test,
            'failed_parameters' => $evaluationResult['failed_parameters'],
        ];
    }

    /**

     *
     * Evaluates test data against a collection of standards.
     * This is a private helper method to avoid code duplication in store() and update().
     *
     * @param array $testData The data from the test (e.g., ['ph_level' => 7.5, ...]).
     * @param Collection $standards The collection of TestingParameter models to check against.
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


    public function destroy(int $id)
    {
        $test = WaterQualityTest::findOrFail($id);
        $test->delete();

        Cache::forget("water_quality_test_{$id}");
        Cache::forget('water_quality_tests_' . md5(json_encode([])));

        return true;
    }


/**
 * Get a detailed report for a specific test and dispatch an email job.
 * This method serves as both an API data source and an event trigger.
 *
 * @param int $id The ID of the WaterQualityTest.
 * @return WaterQualityTest
 */

public function getReportAndDispatchEmail(int $id): WaterQualityTest
{
    $test = WaterQualityTest::with([
        'waterSource.parameters'
    ])->findOrFail($id);
    if (Auth::check()) {
        SendDetailedReportJob::dispatch($test,  Auth::user());
    }
    return $test;
}

}
