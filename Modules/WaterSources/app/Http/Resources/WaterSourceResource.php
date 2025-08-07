<?php
namespace  Modules\WaterSources\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaterSourceResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'source' => $this->source,
            'location' => $this->location,
            'capacity_per_day' => $this->capacity_per_day,
            'capacity_per_hour' => $this->capacity_per_hour,
            'operating_date' => $this->operating_date,
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];

        // ];
    }
}
