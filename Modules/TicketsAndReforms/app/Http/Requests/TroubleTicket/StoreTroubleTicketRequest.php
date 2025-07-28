<?php

namespace Modules\TicketsAndReforms\Http\Requests\TroubleTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreTroubleTicketRequest extends FormRequest
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
            'subject'                   => ['required','string','in:leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other'],
            'body'                      => ['required','string','max:1000'],
            'type'                      => ['in:complaint,trouble'],
            'location.type'             => ['required', 'in:Point'],
            'location.coordinates'      => ['required','array','size:2'],
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
            'subject.required'                  => 'The subject is required please.',
            'subject.in'                        => 'The subject must be one of (leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other)',
            'body.required'                     => 'The body is required please.',
            'body.max'                          => 'The length of the body may not be more than 1000 characters.',
            'type.in'                           => 'The type must be one of (complaint,trouble)',
            'location.type.required'            => 'The location is required please.',
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

