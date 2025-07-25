<?php

namespace Modules\WaterSources\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\WaterSources\Models\TestingParameter;
use Modules\WaterSources\Events\TestingParameterCreated;

class TestingParameterService
{
    public function handle() {}

    public function index()
    {
        return Cache::remember('testing_parameters_all', 3600, function () {
            return TestingParameter::latest()->get();
        });
    }

    public function show($id)
    {
        return Cache::remember("testing_parameter_{$id}", 3600, function () use ($id) {
            return TestingParameter::findOrFail($id);
        });
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $parameter = TestingParameter::create($data);
            Cache::forget('testing_parameters_all');
            DB::commit();
            return $parameter;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function update($id, array $data)
    {
        $parameter = TestingParameter::findOrFail($id);
        $parameter->update($data);
        Cache::forget("testing_parameter_{$id}");
        Cache::forget('testing_parameters_all');
        return $parameter;
    }

    public function destroy($id)
    {
        $parameter = TestingParameter::findOrFail($id);
        $parameter->delete();
        Cache::forget("testing_parameter_{$id}");
        Cache::forget('testing_parameters_all');
        return true;
    }

}
