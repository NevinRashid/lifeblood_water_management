<?php

namespace Modules\DistributionNetwork\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\WaterDistributionOperations\Models\RouteDelivered;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
// use Modules\DistributionNetwork\Database\Factories\DistributionPointFactory;

class DistributionPoint extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'type',
        'status',
        'distribution_network_id'
    ];

    protected $casts = [
        'location' => Point::class,
        'type' => 'string',
        'status' => 'string',
    ];

    public array $translatable = ['name'];

    /** The network this point belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class, 'distribution_network_id');
    }

    /** All water deliveries to this point */
    public function deliveries(): HasMany
    {
        return $this->hasMany(RouteDelivered::class);
    }

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
