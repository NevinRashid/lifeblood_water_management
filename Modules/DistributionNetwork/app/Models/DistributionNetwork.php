<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'zone'
    ];

    protected $casts = [
        'zone' => 'polygon'
    ];

    /** Get all reservoirs in this network */
    public function reservoirs(): HasMany
    {
        return $this->hasMany(Reservoir::class);
    }

    /** Get all distribution points in this network */
    public function distributionPoints(): HasMany
    {
        return $this->hasMany(DistributionPoint::class);
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
        return $this->hasMany(Pipe::class);
    }

    // protected static function newFactory(): DistributionNetworkFactory
    // {
    //     // return DistributionNetworkFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
