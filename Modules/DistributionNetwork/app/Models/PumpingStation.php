<?php

namespace Modules\DistributionNetwork\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Sensors\Models\Sensor;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\PumpingStationFactory;

class PumpingStation extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'status',
        'distribution_network_id',
        'current_pressure',
        'current_flow',
        'max_flow',
        'min_flow',
        'max_pressure',
        'min_pressure',

    ];

    protected $casts = [
        'location' => Point::class,
    ];

    public array $translatable = ['name'];

    /** The network this station belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    public function sensors(): MorphMany
    {
        return $this->morphMany(Sensor::class, 'sensorable');
    }

    // protected static function newFactory(): PumpingStationFactory
    // {
    //     // return PumpingStationFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
