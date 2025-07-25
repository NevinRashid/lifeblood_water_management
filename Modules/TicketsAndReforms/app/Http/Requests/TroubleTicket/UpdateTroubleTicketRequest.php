<?php

namespace Modules\TicketsAndReforms\Http\Requests\TroubleTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTroubleTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'                   => ['nullable','string','in:leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other'],
            //'status'                    => ['in:new, waiting_assignment, assigned, in_progress, fixed, rejected'],
            'body'                      => ['nullable','string','max:1000'],
            'location.type'             => ['nullable', 'in:Point'],
            'location.coordinates'      => ['nullable','array','size:2'],
            'location.coordinates.0'    => ['numeric'],//lng
            'location.coordinates.1'    => ['numeric'],//lat
        ];
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages():array
    {
        return[
            'subject.in'                        => 'The subject must be one of (leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other)',
            'status.in'                         => 'The status must be one of (new, waiting_assignment, assigned, in_progress, fixed, rejected)',
            'body.max'                          => 'The length of the body may not be more than 1000 characters.',
            'location.type.in'                  => 'The location type must be "Point".',
            'location.coordinates.array'        => 'The coordinates must be an array.',
            'location.coordinates.size'         => 'Each point must contain exactly two values.',
            'location.coordinates.0.numeric'    => 'The longitude must be a numeric value.',
            'location.coordinates.1.numeric'    => 'The latitude must be a numeric value.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json
            ([
                'success' => false,
                'message' => 'Data validation error',
                'errors'  => $validator->errors()
            ] , 422));
    }

}


