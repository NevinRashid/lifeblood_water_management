<?php

namespace Modules\WaterSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// use Modules\WaterSources\Database\Factories\TestingParameterFactory;

class TestingParameter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'minimum_level',
        'maximum_level'
    ];

    /**
     * Water sources that use this parameter
     */
    public function waterSources(): BelongsToMany
    {
        return $this->belongsToMany(
            WaterSource::class,
            'water_source_parameters'
        );
    }
    // protected static function newFactory(): TestingParameterFactory
    // {
    //     // return TestingParameterFactory::new();
    // }
}
