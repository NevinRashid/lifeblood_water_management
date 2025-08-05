<?php

namespace Modules\WaterDistributionOperations\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DistributionNetwork\Models\DistributionPoint;

// use Modules\WaterDistributionOperations\Database\Factories\RouteDeliveredFactory;

class RouteDelivered extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    protected $table = 'route_deliveries';
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

    }
     protected function waterAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => number_format($attributes['water_amount_delivered'], 2) . ' Liters'
        );
    }


    protected function arrivalTimeHuman(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => isset($attributes['arrival_time'])
                ? Carbon::parse($attributes['arrival_time'])->diffForHumans()
                : null
        );
    }


    protected function notes(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => trim($value)
        );
    }

}
