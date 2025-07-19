<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;

// use Modules\Beneficiaries\Database\Factories\WaterQuotaFactory;

class WaterQuota extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'received_volume',
        'allocation_date',
        'status',
        'notes',
        'beneficiary_id',
        'delivery_route_id'
    ];

    protected $casts = [
        'allocation_date' => 'datetime'
    ];

    /**
     * The beneficiary receiving this water quota
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * The delivery route used for this water allocation
     */
    public function deliveryRoute(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    // protected static function newFactory(): WaterQuotaFactory
    // {
    //     // return WaterQuotaFactory::new();
    // }
}
