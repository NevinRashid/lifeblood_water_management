<?php

namespace Modules\TicketsAndReforms\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\DistributionNetwork\Models\DistributionNetwork;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\TicketsAndReforms\Database\Factories\TroubleTicketFactory;

class TroubleTicket extends Model
{
    use HasFactory, LogsActivity, HasTranslations, HasSpatial;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
        'body',
        'location',
        'status',
        'user_id',
        'type',
    ];

    protected $casts = [
        'location' => Point::class,
    ];

    /**
     * This is accessor to get the distributionNetwork name 
     */
    public function getNetworkAttribute()
    {
        $network = $this->getNetwork();
        return $network ? ($network->name . " | " . $network->address) : null;
    }

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
     * get associated DistributionNetwork
     */
    public function getNetwork()
    {
        $user = auth()->user();

        $query = DistributionNetwork::query()
            ->whereRaw('ST_Contains(zone, ST_GeomFromText(?))', [
                "POINT({$this->location->latitude} {$this->location->longitude})"
            ]);
            
        if ($user->hasRole('manager')) {
            $query->where('manager_id', $user->id);
        }
        //dd($query->toSql(), $query->getBindings());
        return $query->first();
    }

    /**
     * This method is to clean the body from harmful tags.
     */
    protected function body(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strip_tags($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
        // Chain fluent methods for configuration options
    }
}
