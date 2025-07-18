<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

// use Modules\Sensors\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_id',
        'name',
        'location',
        'sensor_type',
        'status',
        'sensorable_id',
        'sensorable_type'
    ];

    protected $casts = [
        'location' => 'point'
    ];

    /**
     * Get all readings from this sensor
     */
    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    /**
     * Get the parent sensorable model
     * (can be Reservoir, Pipe, Valve etc.)
     */
    public function sensorable(): MorphTo
    {
        return $this->morphTo();
    }

    // protected static function newFactory(): SensorFactory
    // {
    //     // return SensorFactory::new();
    // }
}
