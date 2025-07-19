<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\WaterDistributionOperations\Models\RouteDelivered;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\DistributionPointFactory;

class DistributionPoint extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'type',
        'status',
        'distribution_network_id'
    ];

    protected $casts = [
        'location' => 'point'
    ];

    /** The network this point belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    /** All water deliveries to this point */
    public function deliveries(): HasMany
    {
        return $this->hasMany(RouteDelivered::class);
    }

    // protected static function newFactory(): DistributionPointFactory
    // {
    //     // return DistributionPointFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
