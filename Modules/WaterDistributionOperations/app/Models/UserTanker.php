<?php

namespace Modules\WaterDistributionOperations\Models;

use Spatie\Activitylog\LogOptions;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;


// use Modules\WaterDistributionOperations\Database\Factories\UserTankerFactory;

class UserTanker extends Pivot
{
    use HasFactory,LogsActivity,HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'user_tanker';

    protected $fillable = [
        'user_id',
        'tanker_id'
    ];

    /**
     * The user assigned to the tanker
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tanker assigned to the user
     */
    public function tanker()
    {
        return $this->belongsTo(Tanker::class);
    }


    // protected static function newFactory(): UserTankerFactory
    // {
    //     // return UserTankerFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
