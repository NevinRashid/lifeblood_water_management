<?php

namespace Modules\Sensors\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\DistributionNetwork\Models\Pipe;
use Modules\DistributionNetwork\Models\PumpingStation;
use Modules\DistributionNetwork\Models\Valve;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\Sensors\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

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
    /*
     -This allows easy interaction with the location as a Point object
     - while storing it properly in the database.
    **/
    protected $casts = [
        'location' => Point::class,
    ];

    public array $translatable = ['name'];

    /**
     * Mapping of sensorable types to their corresponding model classes
     */
    protected static $sensorableMap = [
        'valve' => Valve::class,
        'pipe' => Pipe::class,
        'pumpingstation' => PumpingStation::class,
    ];

    /**
     * Resolve a sensorable type to its corresponding model class
     */
    public static function getSensorableClass(string $type): ?string
    {
        $type = strtolower($type);

        // Handle both short type names and full class names
        if (class_exists($type)) {
            foreach (static::$sensorableMap as $mappedType => $class) {
                if ($class === $type) {
                    return $class;
                }
            }
        }

        return static::$sensorableMap[$type] ?? null;
    }

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
