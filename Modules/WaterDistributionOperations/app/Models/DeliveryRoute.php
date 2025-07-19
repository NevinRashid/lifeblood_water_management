<?php

namespace Modules\WaterDistributionOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Modules\WaterDistributionOperations\Database\Factories\DeliveryRouteFactory;

class DeliveryRoute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'start_time',
        'end_time',
        'path',
        'user_tanker_id',
        'planned_date'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'planned_date' => 'date',
        'path' => 'linestring'
    ];

    /**
     * The tanker-user assignment for this route
     */
    public function userTanker(): BelongsTo
    {
        return $this->belongsTo(UserTanker::class);
    }

    /**
     * Delivery points along this route
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(RouteDelivered::class);
    }
}
