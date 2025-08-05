<?php

namespace Modules\WaterSources\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\WaterSources\Database\Factories\WaterQualityTestFactory;

class WaterQualityTest extends Model
{
    use HasFactory, LogsActivity,HasTranslations;

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

    // /**
    //  * The water source this test belongs to
    //  */
    public function waterSource(): BelongsTo
    {
        return $this->belongsTo(WaterSource::class);
    }


    /**
     * Scope a query to return records for the last N days..
     */
    public function scopeLastNDays($query,$days)
    {
        return $query->whereBetween('test_date',[now()->subDays($days),now()]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }

 


}
