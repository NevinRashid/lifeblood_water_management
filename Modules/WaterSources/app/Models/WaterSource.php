<?php

namespace Modules\WaterSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
// use Modules\WaterSources\Database\Factories\WaterSourceFactory;

class WaterSource extends Model
{
    use HasFactory , HasSpatial ;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'source',
        'location',
        'capacity_per_day',
        'capacity_per_hour',
        'status',
        'operating_date'
    ];

    protected $casts = [
        'location' => 'point', // Cast location as spatial point
    ];
    
    /**
     * Get all extractions from this source
     */
    public function extractions(): HasMany
    {
        return $this->hasMany(WaterExtraction::class);
    }

    /**
     * Get all quality tests for this source
     */
    public function qualityTests(): HasMany
    {
        return $this->hasMany(WaterQualityTest::class);
    }

    /**
     * Get testing parameters associated with this source
     */
    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(
            TestingParameter::class,
            'water_source_parameters'
        );
    }

    // protected static function newFactory(): WaterSourceFactory
    // {
    //     // return WaterSourceFactory::new();
    // }
}
