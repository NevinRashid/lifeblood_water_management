<?php

namespace App\Traits;

trait AutoTranslatesAttributes
{
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->translatable ?? [] as $attribute) {
            $array[$attribute] = $this->getTranslation($attribute, app()->getLocale());
        }

        return $array;
    }
}
