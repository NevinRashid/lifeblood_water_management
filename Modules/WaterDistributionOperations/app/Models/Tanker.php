<?php

namespace Modules\WaterDistributionOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\WaterDistributionOperations\Database\Factories\TankerFactory;

class Tanker extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'license_plate',
        'max_capacity',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'note'
    ];
    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];
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
}
