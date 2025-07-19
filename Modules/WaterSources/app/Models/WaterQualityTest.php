<?php

namespace Modules\WaterSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\WaterSources\Database\Factories\WaterQualityTestFactory;

class WaterQualityTest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'water_source_id',
        'ph_level',
        'dissolved_oxygen',
        'total_dissolved_solids',
        'turbidity',
        'temperature',
        'chlorine',
        'nitrate',
        'total_coliform_bacteria',
        'test_date',
        'meets_standard_parameters'
    ];

    protected $casts = [
        'test_date' => 'datetime',
        'meets_standard_parameters' => 'boolean'
    ];

    /**
     * The water source this test belongs to
     */
    public function waterSource(): BelongsTo
    {
        return $this->belongsTo(WaterSource::class);
    }

    // protected static function newFactory(): WaterQualityTestFactory
    // {
    //     // return WaterQualityTestFactory::new();
    // }
}
