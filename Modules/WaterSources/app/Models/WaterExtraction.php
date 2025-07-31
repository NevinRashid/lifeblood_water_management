<?php

namespace Modules\WaterSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\WaterSources\Database\Factories\WaterExtractionFactory;

class WaterExtraction extends Model
{
    use HasFactory,LogsActivity,HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'extracted',
        'extraction_date',
        'water_source_id'
    ];
    /**
     * The water source this extraction belongs to
     */
    public function waterSource(): BelongsTo
    {
        return $this->belongsTo(WaterSource::class);
    }

    /**
     * Scope a query to return records for the last N days..
     */
    public function scopeLastNDays($query,$days)
    {
        return $query->whereBetween('extraction_date',[now()->subDays($days),now()]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
