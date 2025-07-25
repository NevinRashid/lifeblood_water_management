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
            // 'status_translated' => $this->status_translated, // القيمة المترجمة

            'source' => $this->source,
            // 'source_translated' => $this->source_translated, // القيمة المترجمة

            // 'location' => [
            //     'latitude' => $this->location?->latitude,
            //     'longitude' => $this->location?->longitude,
            // ],
            'location' => $this->location,
            'capacity_per_day' => $this->capacity_per_day,
            'capacity_per_hour' => $this->capacity_per_hour,
            'operating_date' => $this->operating_date,
    //    'media' => $this->whenLoaded('media', function () {
    //             return [
    //                 'images' => $this->getMedia('water_source_images')->map(function ($media) {
    //                     return $media->getFullUrl();
    //                 }),
    //                 'documents' => $this->getMedia('water_source_documents')->map(function ($media) {
    //                     return $media->getFullUrl();
    //                 }),
    //                 'videos' => $this->getMedia('water_source_videos')->map(function ($media) {
    //                     return $media->getFullUrl();
    //                 }),
    //             ];
                    //    }),
    'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
        
        // ];
    }
}
