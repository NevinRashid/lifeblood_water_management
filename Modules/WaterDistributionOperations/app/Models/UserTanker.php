<?php

namespace Modules\WaterDistributionOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\WaterDistributionOperations\Database\Factories\UserTankerFactory;

class UserTanker extends Model
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
