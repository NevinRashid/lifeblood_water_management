<?php

namespace Modules\WaterSources\Services;

use Modules\WaterSources\Models\WaterQualityTest;

class WaterQualityTestService
{
    public function handle() {}

    public function index(array $filters = [])
    {
        $query = WaterQualityTest::query();

        if (!empty($filters['water_source_id'])) {
            $query->where('water_source_id', $filters['water_source_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('test_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('test_date', '<=', $filters['date_to']);
        }

        return $query->latest('test_date')->paginate(10);
    }

    public function show(int $id)
    {
        return WaterQualityTest::findOrFail($id);
    }

    public function store(array $data)
    {
        return WaterQualityTest::create($data);
    }

    public function update(int $id, array $data)
    {
        $test = WaterQualityTest::findOrFail($id);
        $test->update($data);
        return $test;
    }

    public function destroy(int $id)
    {
        $test = WaterQualityTest::findOrFail($id);
        return $test->delete();
    }
}
