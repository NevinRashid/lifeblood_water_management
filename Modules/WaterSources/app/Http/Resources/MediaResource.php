<?php

namespace Modules\WaterSources\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * دالة مساعدة لتنظيف النصوص من أي مشاكل في ترميز UTF-8.
     *
     * @param string|null $value
     * @return string|null
     */
    private function cleanString($value)
    {
        return $value ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $this هنا يشير إلى كائن الميديا الواحد
        return [
            'id' => $this->id,
            'collection_name' => $this->cleanString($this->collection_name),
            'name' => $this->cleanString($this->name), // اسم الملف بعد الحفظ
            'file_name' => $this->cleanString($this->file_name), // اسم الملف الأصلي
            'mime_type' => $this->mime_type,
            'size' => $this->size, // حجم الملف بالبايت
            'original_url' => $this->getFullUrl(), // الرابط الكامل للملف الأصلي
            'preview_url' => $this->hasGeneratedConversion('preview') ? $this->getFullUrl('preview') : null, // رابط صورة المعاينة (إذا وجدت)
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
