<?php

namespace Modules\UsersAndTeams\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\TicketsAndReforms\Models\Reform;
use Modules\TicketsAndReforms\Models\TroubleTicket;
use Modules\UsersAndTeams\Notifications\QueuedResetPassword;
use Modules\UsersAndTeams\Notifications\QueuedVerifyEmail;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\ReservoirActivity;
use Modules\WaterDistributionOperations\Models\Tanker;
use Modules\WaterDistributionOperations\Models\UserTanker;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{

    use HasFactory, HasRoles, HasApiTokens, Notifiable, LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'password',
        'team_id',
        'phone',
        'address',
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
     * to prevent password logging 'only name and email will be logged'
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->useLogName('User');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new QueuedResetPassword($token));
    }

    /**
     * The team this user belongs to (one-to-many)
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
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

    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }


    public function scopeServedByNetwork($query, $networkId)
    {
        return $query->whereHas('beneficiary.distributionPoint', function ($q) use ($networkId) {
            $q->where('type','water tap')
            ->where('distribution_network_id', $networkId);
        });
    }
}
