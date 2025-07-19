<?php

namespace Modules\TicketsAndReforms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\UsersAndTeams\Models\Team;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\TicketsAndReforms\Database\Factories\ReformFactory;

class Reform extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'trouble_ticket_id',
        'description',
        'status',
        'reform_cost',
        'materials_used',
        'team_id',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reform_cost' => 'decimal:2'
    ];

    /**
     * The trouble ticket this reform addresses
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TroubleTicket::class, 'trouble_ticket_id');
    }

    /**
     * The team assigned to this repair work
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // protected static function newFactory(): ReformFactory
    // {
    //     // return ReformFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
