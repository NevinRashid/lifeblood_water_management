<?php

namespace Modules\DistributionNetwork\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Sensors\Models\Sensor;

// use Modules\DistributionNetwork\Database\Factories\ValveFactory;

class Valve extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'is_open',
        'valve_type',
        'status',
        'distribution_network_id',
        'current_flow',
        'max_flow',
        'min_flow'
    ];

    protected $casts = [
        'location' => Point::class, // Casts to/from Point object
    ];

    public array $translatable = ['name'];

    /** The network this valve belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class, 'distribution_network_id');
    }

    public function sensors(): MorphMany
    {
        return $this->morphMany(Sensor::class, 'sensorable');
    }

    // protected static function newFactory(): ValveFactory
    // {
    //     // return ValveFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
