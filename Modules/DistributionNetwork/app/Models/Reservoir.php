<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;

// use Modules\DistributionNetwork\Database\Factories\ReservoirFactory;

class Reservoir extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'tank_type',
        'maximum_capacity',
        'minimum_critical_level',
        'status',
        'distribution_network_id'
    ];

    protected $casts = [
        'location' => 'point'
    ];

    /** The network this reservoir belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    /** All activity logs for this reservoir */
    public function activities(): HasMany
    {
        return $this->hasMany(ReservoirActivity::class);
    }

    // protected static function newFactory(): ReservoirFactory
    // {
    //     // return ReservoirFactory::new();
    // }
}
