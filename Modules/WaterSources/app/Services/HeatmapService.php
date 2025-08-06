<?php

namespace Modules\WaterSources\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Modules\WaterSources\Models\WaterSource;

class HeatmapService
{
    use HandleServiceErrors;

    /**
     * Retrieve water sources that are considered risky based on recent extraction and quality test data.
     * - A source is considered at risk if:
     * - Volume risk: the average daily extraction in the last 30 days exceeds 90% of its capacity.
     * - Quality risk: more than 30% of quality tests in the last 30 days failed.
     * - Determines risk type as 'volume', 'quality', or 'both'.
     * - Results are cached per locale for one day to improve performance.
     *
     * @return array $arraydata A list of risky water sources with details including risk percentages, coordinates, and risk type.
     */
    public function getRiskySources()
    {
        try{
            return Cache::remember('all_risky_sources_'. app()->getLocale(), now()->addDay(), function(){
                    $result = [];
                    $sources= WaterSource::with([
                        'extractions'  => function($q) { return $q->scopeLastNDays(30); },
                        'qualityTests' => function($q) { return $q->scopeLastNDays(30); },
                        ])->get();

                    foreach($sources as $source){
                        $avgExtracted = $source->extractions->avg('extracted')?? 0;
                        $expected = $source->capacity_per_day ?? 0;
                        $ratio = $expected>0 ? $avgExtracted/$expected : 0;
                        $volumeRisk = $ratio>=0.9? round($ratio*100,2):0;

                        $testCount = $source->qualityTests->count();
                        $failedTests = $source->qualityTests()->where('meets_standard_parameters',false)->count();
                        $failureRate = $testCount>0 ? $failedTests/$testCount :0;
                        $qualityRisk = $failureRate>0.3 ? round($failureRate*100,2) :0;

                        // Determine the type of risk
                        if ($volumeRisk > 0 || $qualityRisk > 0) {
                            $riskType = match (true) {
                            $volumeRisk > 0 && $qualityRisk > 0 => 'both',
                            $volumeRisk > 0 => 'volume',
                            $qualityRisk > 0 => 'quality',
                            };
                            $result[] = [
                                'water_source_id' => $source->id,
                                'name'            => $source->name,
                                'status'          => $source->status,
                                'operating_date'  => $source->operating_date,
                                'lat'             => $source->location->latitude,
                                'lng'             => $source->location->longitude,
                                'volume_risk'     => $volumeRisk,
                                'quality_risk'    => $qualityRisk,
                                'risk_type'       => $riskType,
                                ];
                        }
                    }
                    return $result;;
                });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
