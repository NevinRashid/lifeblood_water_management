<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\DistributionNetwork\Database\Factories\ValveFactory;

class Valve extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'is_open',
        'valve_type',
        'status',
        'distribution_network_id',
        'current_flow'
    ];

    protected $casts = [
        'location' => 'point',
        'is_open' => 'boolean'
    ];

    /** The network this valve belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    // protected static function newFactory(): ValveFactory
    // {
    //     // return ValveFactory::new();
    // }
}
