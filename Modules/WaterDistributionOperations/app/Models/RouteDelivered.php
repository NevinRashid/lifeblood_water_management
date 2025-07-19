<?php

namespace Modules\WaterDistributionOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\WaterDistributionOperations\Database\Factories\RouteDeliveredFactory;

class RouteDelivered extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'water_amount_delivered',
        'arrival_time',
        'notes',
        'delivery_route_id',
        'distribution_point_id'
    ];

    protected $casts = [
        'arrival_time' => 'datetime'
    ];

    /**
     * The parent delivery route
     */
    public function deliveryRoute(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    /**
     * The distribution point where delivery occurred
     */
    public function distributionPoint(): BelongsTo
    {
        return $this->belongsTo(DistributionPoint::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
