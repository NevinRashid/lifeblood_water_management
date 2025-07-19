<?php

namespace Modules\UsersAndTeams\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\TicketsAndReforms\Models\Reform;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\UsersAndTeams\Database\Factories\TeamFactory;

class Team extends Model
{
    use HasFactory ,LogsActivity,HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get all users assigned to this team
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_team');
    }

    /**
     * Get all reform tasks assigned to this team
     */
    public function reforms(): HasMany
    {
        return $this->hasMany(Reform::class);
    }

    /**
     * Get all trouble tickets handled by this team
     */
    public function troubleTickets(): HasMany
    {
        return $this->hasMany(TroubleTicket::class);
    }

    // protected static function newFactory(): TeamFactory
    // {
    //     // return TeamFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
