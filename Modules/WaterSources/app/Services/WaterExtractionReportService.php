<?php

namespace Modules\WaterSources\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\WaterSources\Models\WaterExtraction;

class WaterExtractionReportService
{
    /**
     * Generate a water extraction report
     *
     * @param string $periodType   daily|monthly|annual|custom
     * @param string $groupBy      network|source
     * @param Carbon|null $startDate   Used only for custom period
     * @param Carbon|null $endDate     Used only for custom period
     * @param Carbon|null $specificDate Used for daily/monthly/annual
     * @param int|null $networkId   Optional: filter by specific network
     * @return Collection
     */
    public function generateReport(
        string $periodType,
        string $groupBy,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        ?Carbon $specificDate = null,
        ?int $networkId = null
    ): Collection {

        // We build the cache key based on the same inputs (to make it unique)
        $cacheKey = "water_report_{$periodType}_{$groupBy}_" .
            ($startDate?->toDateString() ?? 'null') . '_' .
            ($endDate?->toDateString() ?? 'null') . '_' .
            ($specificDate?->toDateString() ?? 'null') . '_' .
            ($networkId ?? 'all');

        // Start query
        return Cache::remember($cacheKey, now()->addHour(), function () use (
            $periodType,
            $groupBy,
            $startDate,
            $endDate,
            $specificDate,
            $networkId
        ) {
            // Start query
            $query = WaterExtraction::query();

            // Filter by network if provided
            if ($networkId) {
                $query->where('distribution_network_id', $networkId);
            }

            // Apply date filters
            if ($periodType === 'custom' && $startDate && $endDate) {
                $query->whereBetween('extraction_date', [$startDate, $endDate]);
            } elseif ($specificDate) {
                if ($periodType === 'daily') {
                    $query->whereDate('extraction_date', $specificDate);
                } elseif ($periodType === 'monthly') {
                    $query->whereYear('extraction_date', $specificDate->year)
                        ->whereMonth('extraction_date', $specificDate->month);
                } elseif ($periodType === 'annual') {
                    $query->whereYear('extraction_date', $specificDate->year);
                }
            }

            // Select columns
            $columns = [];

            if ($periodType === 'daily') {
                $columns[] = DB::raw('DATE(extraction_date) as date');
            } elseif ($periodType === 'monthly') {
                $columns[] = DB::raw('YEAR(extraction_date) as year');
                $columns[] = DB::raw('MONTH(extraction_date) as month');
            } elseif ($periodType === 'annual') {
                $columns[] = DB::raw('YEAR(extraction_date) as year');
            }

            $columns[] = $groupBy === 'network'
                ? 'distribution_network_id'
                : 'water_source_id';

            $columns[] = DB::raw('SUM(extracted) as total_extracted');
            $columns[] = DB::raw('SUM(delivered_amount) as total_delivered');
            $columns[] = DB::raw('SUM(lost_amount) as total_lost');
            $columns[] = DB::raw('CASE WHEN SUM(extracted) > 0 
            THEN ROUND(SUM(lost_amount)/SUM(extracted)*100, 2)
            ELSE 0 END as loss_percentage');

            $results = $query
                ->select($columns)
                ->groupBy($this->getGroupByColumns($periodType, $groupBy))
                ->get();

            return $results->map(function ($item) use ($periodType, $groupBy) {
                $result = [
                    'total_extracted' => (float) $item->total_extracted,
                    'total_delivered' => (float) $item->total_delivered,
                    'total_lost' => (float) $item->total_lost,
                    'loss_percentage' => (float) $item->loss_percentage,
                    'group' => $groupBy === 'network'
                        ? [
                            'id' => $item->distribution_network_id,
                            'name' => optional($item->network)->name
                        ]
                        : [
                            'id' => $item->water_source_id,
                            'name' => optional($item->waterSource)->name
                        ],
                ];

                if ($periodType === 'daily') {
                    $result['period'] = ['date' => $item->date];
                } elseif ($periodType === 'monthly') {
                    $result['period'] = [
                        'year' => $item->year,
                        'month' => $item->month,
                    ];
                } elseif ($periodType === 'annual') {
                    $result['period'] = ['year' => $item->year];
                }

                return $result;
            });
        });
    }

    /**
     * Helper method to get group by columns
     */
    private function getGroupByColumns(string $periodType, string $groupBy): array
    {
        $groups = [];

        if ($periodType === 'daily') {
            $groups[] = DB::raw('DATE(extraction_date)');
        } elseif ($periodType === 'monthly') {
            $groups[] = DB::raw('YEAR(extraction_date)');
            $groups[] = DB::raw('MONTH(extraction_date)');
        } elseif ($periodType === 'annual') {
            $groups[] = DB::raw('YEAR(extraction_date)');
        }

        $groups[] = $groupBy === 'network'
            ? 'distribution_network_id'
            : 'water_source_id';

        return $groups;
    }
}
