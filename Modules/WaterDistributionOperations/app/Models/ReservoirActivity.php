<?php

namespace Modules\WaterDistributionOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\DistributionNetwork\Models\Reservoir;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\WaterDistributionOperations\Database\Factories\ReservoirActivityFactory;

class ReservoirActivity extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    protected $table = 'reservoirs_activity';

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'activity_level',
        'activity_time',
        'amount',
        'triggered_by',
        'activity_type',
        'notes',
        'reservoir_id',
        'user_id'
    ];

    protected $casts = [
        'activity_time' => 'datetime'
    ];

    /**
     * The reservoir where activity occurred
     */
    public function reservoir(): BelongsTo
    {
        return $this->belongsTo(Reservoir::class);
    }

    /**
     * User who triggered the activity (if manual)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
