<?php

namespace Modules\TicketsAndReforms\Models;

use App\Traits\AutoTranslatesAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\UsersAndTeams\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

// use Modules\TicketsAndReforms\Database\Factories\TroubleTicketFactory;

class TroubleTicket extends Model
{
    use HasFactory,LogsActivity, HasTranslations,HasSpatial, AutoTranslatesAttributes;

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

    public array $translatable = ['body'];

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
     * This method is to clean the body from harmful tags.
     */
    protected function body(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strip_tags($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }
}
