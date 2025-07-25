<?php

namespace Modules\UsersAndTeams\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'name',
        'description',
        'status',
    ];

    /**
     * Get all users assigned to this team
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class);
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

    /**
     * This method is to clean the description from harmful tags.
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strip_tags($value),
        );
    }

    /**
     * This function ensures that the name is always in a specified format
     *  (the first letter is uppercase when reading, all letters are lowercase when writing)
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
