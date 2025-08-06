<?php

namespace Modules\TicketsAndReforms\Http\Requests\TroubleTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateTroubleTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user=Auth::user();
        return $user->can('update_trouble_ticket');
    }

    public function rules(): array
    {
        return [
            'subject'                   => ['nullable','string','in:leak,pipe_breaks, water_outages, low_pressure, overflow, sensor_failure, other'],
            'body'                      => ['nullable','string','max:1000'],
            'location'                  => ['nullable','array'],
            'location.lat'              => ['nullable','numeric','between:-90,90'],
            'location.lng'              => ['nullable','numeric','between:-180,180'],
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
            'body.max'                          => 'The length of the body may not be more than 1000 characters.',
            'type.in'                           => 'The type must be one of (complaint,trouble)',
            'location.array'                    => 'The location must be an array.',
            'location.lat.numeric'              => 'The longitude must be a numeric value.',
            'location.lng.numeric'              => 'The latitude must be a numeric value.',
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


