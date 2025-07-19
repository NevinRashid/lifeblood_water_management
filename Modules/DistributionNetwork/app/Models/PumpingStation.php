<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\DistributionNetwork\Database\Factories\PumpingStationFactory;

class PumpingStation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'status',
        'distribution_network_id',
        'current_pressure',
        'current_flow'
    ];

    protected $casts = [
        'location' => 'point'
    ];

    /** The network this station belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    // protected static function newFactory(): PumpingStationFactory
    // {
    //     // return PumpingStationFactory::new();
    // }
}
