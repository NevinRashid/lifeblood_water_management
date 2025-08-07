<?php

namespace Modules\DistributionNetwork\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Sensors\Models\Sensor;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\PipeFactory;

class Pipe extends Model
{
    use HasFactory, LogsActivity, HasTranslations, HasSpatial, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'path',
        'distribution_network_id',
        'current_pressure',
        'current_flow',
        'max_flow',
        'min_flow',
        'max_pressure',
        'min_pressure'
    ];

    protected $casts = [
        'path' => LineString::class
    ];

    public array $translatable = ['name'];

    /** The network this pipe belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class, 'distribution_network_id');
    }

    public function sensors(): MorphMany
    {
        return $this->morphMany(Sensor::class, 'sensorable');
    }

    // protected static function newFactory(): PipeFactory
    // {
    //     // return PipeFactory::new();
    // }

    /**
     * This function ensures that the name is always in a specified format
     *  (the first letter is uppercase when reading, all letters are lowercase when writing)
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
