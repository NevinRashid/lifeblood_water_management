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
        $user=Auth::user();
        return $user->can('create_trouble_ticket');
    }

    public function rules(): array
    {
        $rules = [
            'subject'       => ['required','string','in:leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other'],
            'body'          => ['required','string','max:1000'],
            'location'      => ['required','array'],
            'location.lat'  => ['required','numeric','between:-90,90'],
            'location.lng'  => ['required','numeric','between:-180,180'],
        ];

        //If the user is a citizen, the type of report must be entered, either a complaint or a trouble.
        // If the user is a field team, the default status is trouble.
        if(Auth::user()->hasRole('Affected Community Member')){
            $rules['type']= ['required','in:complaint,trouble'];
        }
        return $rules;
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages():array
    {
        return[
            'subject.required'     => 'The subject is required please.',
            'subject.in'           => 'The subject must be one of (leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other)',
            'body.required'        => 'The body is required please.',
            'body.max'             => 'The length of the body may not be more than 1000 characters.',
            'type.required'        => 'The type is required please.',
            'type.in'              => 'The type must be one of (complaint,trouble)',
            'location.required'    => 'The location is required please.',
            'location.array'       => 'The location must be an array.',
            'location.lat.numeric' => 'The longitude must be a numeric value.',
            'location.lng.numeric' => 'The latitude must be a numeric value.',
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

