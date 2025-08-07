<?php

namespace Modules\WaterDistributionOperations\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

// use Modules\WaterDistributionOperations\Database\Factories\DeliveryRouteFactory;

class DeliveryRoute extends Model
{
    use HasFactory, LogsActivity, HasTranslations, HasSpatial, AutoTranslatesAttributes;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'start_time',
        'end_time',
        'path',
        'user_tanker_id',
        'planned_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'planned_date' => 'date',
        'path' => LineString::class,
    ];

    /**
     * The attributes that are spatial.
     *
     * @var array
     */
    protected $spatialFields = [
        'path',
    ];

    public array $translatable = ['name', 'description'];

    /**
     * The tanker-user assignment for this route
     */
    public function userTanker(): BelongsTo
    {
        return $this->belongsTo(UserTanker::class);
    }

    /**
     * Delivery points along this route
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(RouteDelivered::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
