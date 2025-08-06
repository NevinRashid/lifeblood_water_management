<x-mail::message>
# @lang('mail.low_water.subject', ['sourceName' => $waterSource->getTranslation('name', app()->getLocale())])

@lang('mail.low_water.greeting')

@lang('mail.low_water.intro', ['sourceName' => $waterSource->getTranslation('name', app()->getLocale())])

- **@lang('mail.low_water.capacity')** {{ number_format($waterSource->capacity_per_day, 2) }} m<sup>3</sup>
- **@lang('mail.low_water.extracted')** {{ number_format($totalExtractedToday, 2) }} m<sup>3</sup>

@lang('mail.low_water.action_needed')

@lang('mail.low_water.regards')<br>
{{ config('app.name') }}
</x-mail::message>