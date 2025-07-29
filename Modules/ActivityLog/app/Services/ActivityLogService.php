<?php

namespace Modules\ActivityLog\Services;

use App\Services\Base\BaseService;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService extends BaseService
{

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }


    public function query(array $filters = [])
    {
        $query = parent::query($filters);

        return $this->handle(function () use ($filters, $query) {
            return $query
                ->when(isset($filters['log_name']), fn($q) => $q->where('log_name', $filters['log_name']))

                ->when(isset($filters['event']), fn($q) => $q->where('event', $filters['event']))

                ->when(isset($filters['sort_by']), function ($q) use ($filters) {

                    $direction = $filters['sort_direction'] ?? 'desc';

                    $q->orderBy($filters['sort_by'], $direction);
                });
        });
    }
}
