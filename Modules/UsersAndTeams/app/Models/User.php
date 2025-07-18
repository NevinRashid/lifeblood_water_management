<?php

namespace Modules\UsersAndTeams\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\TicketsAndReforms\Models\Reform;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;
use Modules\WaterDistributionOperations\Models\Tanker;
use Modules\WaterDistributionOperations\Models\UserTanker;

class User extends Authenticatable
{
    /** @use HasFactory<\Modules\UsersAndTeams\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Teams the user belongs to (Many-to-Many)
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_team');
    }

    /**
     * Trouble tickets created by the user (One-to-Many)
     */
    public function troubleTickets(): HasMany
    {
        return $this->hasMany(TroubleTicket::class);
    }

    /**
     * Reservoir activities performed by the user (One-to-Many)
     */
    public function reservoirActivities(): HasMany
    {
        return $this->hasMany(ReservoirActivity::class);
    }

    /**
     * Delivery routes managed by the user (One-to-Many)
     */
    public function deliveryRoutes(): HasMany
    {
        return $this->hasMany(DeliveryRoute::class, 'user_id');
    }

    /**
     * Repair reforms assigned to the user (One-to-Many)
     */
    public function assignedReforms(): HasMany
    {
        return $this->hasMany(Reform::class, 'assigned_user_id');
    }

    public function tankers(): BelongsToMany
    {
        return $this->belongsToMany(Tanker::class, 'user_tanker')
            ->using(UserTanker::class)
            ->withTimestamps();
    }
}
