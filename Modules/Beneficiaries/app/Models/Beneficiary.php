<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\Beneficiaries\Database\Factories\BeneficiaryFactory;

class Beneficiary extends Model
{
    use HasFactory, LogsActivity, HasTranslations, HasSpatial;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'household_size',
        'children_count',
        'elderly_count',
        'disabled_count',
        'location',
        'address',
        'benefit_type',
        'distribution_point_id',
        'status',
        'notes',
        'additional_data',
        'user_id',
    ];

    protected $casts = [
        'location' => Point::class,
    ];

    /**
     * The distribution point where beneficiary receives water
     */
    public function distributionPoint(): BelongsTo
    {
        return $this->belongsTo(DistributionPoint::class);
    }

    /**
     * All water quotas allocated to this beneficiary
     */
    public function waterQuotas(): HasMany
    {
        return $this->hasMany(WaterQuota::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // protected static function newFactory(): BeneficiaryFactory
    // {
    //     // return BeneficiaryFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
