<?php

namespace Modules\WaterSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\WaterSources\Database\Factories\WaterExtractionFactory;

class WaterExtraction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'extracted',
        'extraction_date',
        'water_source_id'
    ];
    /**
     * The water source this extraction belongs to
     */
    public function waterSource(): BelongsTo
    {
        return $this->belongsTo(WaterSource::class);
    }
    // protected static function newFactory(): WaterExtractionFactory
    // {
    //     // return WaterExtractionFactory::new();
    // }
}
