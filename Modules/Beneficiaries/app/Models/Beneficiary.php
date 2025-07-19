<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\DistributionNetwork\Models\DistributionPoint;

// use Modules\Beneficiaries\Database\Factories\BeneficiaryFactory;

class Beneficiary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
        protected $fillable = [
        'family_name',
        'contact_phone',
        'location',
        'number_of_individuals',
        'benefit_type',
        'distribution_point_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'location' => 'point',
        'allocation_date' => 'datetime'
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

    // protected static function newFactory(): BeneficiaryFactory
    // {
    //     // return BeneficiaryFactory::new();
    // }
}
