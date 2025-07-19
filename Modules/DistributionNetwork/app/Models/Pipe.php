<?php

namespace Modules\DistributionNetwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\DistributionNetwork\Database\Factories\PipeFactory;

class Pipe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'path',
        'distribution_network_id',
        'current_pressure',
        'current_flow'
    ];

    protected $casts = [
        'path' => 'linestring'
    ];

    /** The network this pipe belongs to */
    public function network(): BelongsTo
    {
        return $this->belongsTo(DistributionNetwork::class);
    }

    // protected static function newFactory(): PipeFactory
    // {
    //     // return PipeFactory::new();
    // }
}
