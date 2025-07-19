<?php

namespace Modules\TicketsAndReforms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\TicketsAndReforms\Database\Factories\TroubleTicketFactory;

class TroubleTicket extends Model
{
    use HasFactory,LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
        'body',
        'location',
        'status',
        'ticketable_id',
        'ticketable_type',
        'user_id'
    ];

    protected $casts = [
        'location' => 'point',
    ];

    /**
     * The user who created this ticket
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The associated repair/reform work order
     */
    public function reform(): HasOne
    {
        return $this->hasOne(Reform::class);
    }

    /**
     * The related asset (polymorphic)
     * (Pipe, Valve, Reservoir etc.)
     */
    public function ticketable(): MorphTo
    {
        return $this->morphTo();
    }

    // protected static function newFactory(): TroubleTicketFactory
    // {
    //     // return TroubleTicketFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
