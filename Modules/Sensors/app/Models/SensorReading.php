<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\Sensors\Database\Factories\SensorReadingFactory;

class SensorReading extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    protected $table = 'sensors_readings';
    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'value',
        'unit',
        'recorded_at',
        'sensor_id'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    /**
     * The sensor that recorded this reading
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    // protected static function newFactory(): SensorReadingFactory
    // {
    //     // return SensorReadingFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
