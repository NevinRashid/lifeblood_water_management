<?php

namespace Modules\WaterDistributionOperations\Models;

use App\Traits\AutoTranslatesAttributes;
use Spatie\Activitylog\LogOptions;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// use Modules\WaterDistributionOperations\Database\Factories\TankerFactory;

class Tanker extends Model
{
    use HasFactory, LogsActivity, HasTranslations, AutoTranslatesAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'license_plate',
        'max_capacity',
        'status',
        'current_location',
        'last_maintenance_date',
        'next_maintenance_date',
        'note'
    ];
    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'current_location' => Point::class,
    ];

    public array $translatable = ['note'];
    /**
     * Users assigned to this tanker
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tanker')
            ->using(UserTanker::class)
            ->withTimestamps();
    }

    /**
     * Delivery routes for this tanker
     */
    public function deliveryRoutes(): HasMany
    {
        return $this->hasMany(DeliveryRoute::class, 'user_tanker_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
    protected function isAvailable(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['status'] === 'available',
        );
    }

    protected function licensePlate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtoupper(str_replace(' ', '', $value)),
        );
    }
}
