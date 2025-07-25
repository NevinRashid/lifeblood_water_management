<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Sensors\Models\Sensor;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\PipeFactory;

class Pipe extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'path',
        'distribution_network_id',
        'current_pressure',
        'current_flow'
    ];

    protected $casts = [
        'path' => 'linestring'
    ];

    /** The network this pipe belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    public function sensors(): MorphMany
    {
        return $this->morphMany(Sensor::class, 'sensorable');
    }

    // protected static function newFactory(): PipeFactory
    // {
    //     // return PipeFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
