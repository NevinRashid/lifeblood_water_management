<?php

namespace Modules\WaterSources\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\DistributionNetwork\Models\DistributionNetwork;

// use Modules\WaterSources\Database\Factories\WaterSourceFactory;

class WaterSource extends Model implements HasMedia
{
    use HasFactory , HasSpatial, LogsActivity ,HasTranslations,InteractsWithMedia;

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
        /**
         *
         * @var array
         */
        protected $casts = [
            'location' => Point::class,
            'operating_date' => 'date',
        ];
    /**
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('water_source_documents');
        $this->addMediaCollection('water_source_images');
        $this->addMediaCollection('water_source_videos');
    }


    /**
     * Get all extractions from this source
     */
    public function extractions(): HasMany
    {
        return $this->hasMany(WaterExtraction::class);
    }

    // /**
    //  * Get all quality tests for this source
    //  */
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

    /**
     * Get all distribution networks from this source
     */
    public function networks(): HasMany
    {
        return $this->hasMany(DistributionNetwork::class,'water_source_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'source', 'status', 'operating_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
