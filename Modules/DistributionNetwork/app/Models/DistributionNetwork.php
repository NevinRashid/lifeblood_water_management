<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Modules\WaterSources\Models\WaterSource;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\DistributionNetworkFactory;

class DistributionNetwork extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'address',
        'zone',
        'water_source_id',
        'manager_id',
    ];

    protected $casts = [
        'zone' => Polygon::class
    ];

    /** Get all reservoirs in this network */
    public function reservoirs(): HasMany
    {
        return $this->hasMany(Reservoir::class);
    }

    /** Get all distribution points in this network */
    public function distributionPoints(): HasMany
    {
        return $this->hasMany(DistributionPoint::class,'distribution_network_id');
    }

    /** Get all pumping stations in this network */
    public function pumpingStations(): HasMany
    {
        return $this->hasMany(PumpingStation::class);
    }

    /** Get all valves in this network */
    public function valves(): HasMany
    {
        return $this->hasMany(Valve::class);
    }

    /** Get all pipes in this network */
    public function pipes(): HasMany
    {
        return $this->hasMany(Pipe::class,'distribution_network_id');
    }

    /** The source this network belongs to */
    public function source(): BelongsTo
    {
        return $this->belongsTo(WaterSource::class,'water_source_id');
    }
    /**
     * Get the manager of this network
     */
    public function manager():BelongsTo
    {
        return $this->belongsTo(User::class,'manager_id');
    }

    /**
     * This function ensures that the name is always in a specified format
     *  (the first letter is uppercase when reading, all letters are lowercase when writing)
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
