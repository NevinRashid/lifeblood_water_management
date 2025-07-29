<?php

namespace Modules\ActivityLog\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\ActivityLog\Enums\LogEvent;
use Modules\ActivityLog\Enums\LogName;

class FilterActivityLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'log_name' => ['nullable', Rule::in(LogName::all())],
            'event' => ['nullable', Rule::in(LogEvent::all())],

            'sort_by'         => 'nullable|string|in:created_at',
            'sort_direction'  => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|numeric|min:11'
        ];
    }

    public function attributes()
    {
        return [
            'log_name' => 'Log Name',
            'event' => 'Event',
            'sort_by' => 'Sort By',
            'sort_direction' => 'Sort Direction',
            'per_page' => 'Per Page',
        ];
    }

    public function messages()
    {
        return [
            'log_name.in' => 'The :attribute must be one of ' . implode(', ', LogName::all()),
            'event.in' => 'The :attribute must be one of ' . implode(', ', LogEvent::all()),
            'sort_by.in' => 'The :attribute must be one of: created_at',
            'sort_direction.in' => 'The :attribute must be one of: asc, desc'
        ];
    }

    /**
     * if the validation failed it return a json response
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed Validate Data',
            'errors' => $validator->errors(),
        ], 422));
    }
}
