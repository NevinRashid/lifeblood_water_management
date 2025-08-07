<?php

namespace Modules\DistributionNetwork\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;
use Point;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\DistributionNetwork\Database\Factories\ReservoirFactory;

class Reservoir extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'tank_type',
        'maximum_capacity',
        'minimum_critical_level',
        'status',
        'distribution_network_id'
    ];

    protected $casts = [
        'location' => Point::class,
    ];

    public array $translatable = ['name'];

    /** The network this reservoir belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    /** All activity logs for this reservoir */
    public function activities(): HasMany
    {
        return $this->hasMany(ReservoirActivity::class);
    }

    // Accessor to current level
    // public function getCurrentLevelAttribute(): float
    // {
    //     return app(ReservoirsActivityService::class)->calculateCurrentLevel($this->id);
    // }

    
    // protected static function newFactory(): ReservoirFactory
    // {
    //     // return ReservoirFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
