<?php

namespace App\Traits;

/**
 * Automatically translates specified attributes when a model is converted to an array
 *
 * To use this, define a `$translatable` array property on your model that
 * lists all the attribute keys you want to be auto-translated
 *
 * Note: This trait deal with model uses a translation package (like spatie/laravel-translatable)
 * that provides a `getTranslation` method
 */
trait AutoTranslatesAttributes
{
    /**
     * Override the default toArray method to inject translated attributes
     *
     * This ensures that when the model is cast to an array or JSON, the
     * translatable attributes are returned in the current app locale
     *
     * @return array The model's array representation with translated values
     */
    public function toArray()
    {
        // get the standard array representation from the parent
        $array = parent::toArray();

        // Loop through the attributes defined as translatable on the model
        foreach ($this->translatable ?? [] as $attribute) {
            // Overwrite the original attribute with its translation for the current locale
            $array[$attribute] = $this->getTranslation($attribute, app()->getLocale());
        }

        return $array;
    }
}