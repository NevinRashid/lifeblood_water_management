<?php

namespace Modules\TicketsAndReforms\Http\Requests\Reform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Modules\TicketsAndReforms\Rules\TroubleTicketNotRejected;

class StoreReformRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user=Auth::user();
        return $user->can('create_reform');
    }

    public function rules(): array
    {
        return [
            'team_id'               => ['required', 'integer','exists:teams,id'],
            'trouble_ticket_id'     => ['required', 'integer','unique:reforms','exists:trouble_tickets,id',new TroubleTicketNotRejected()],
            'expected_start_date'   => ['required','date','after_or_equal:today'],
            'expected_end_date'     => ['required','date','after_or_equal:expected_start_date'],
            'reform_cost'           => ['nullable','numeric'],
            'before_images'         => ['nullable','array'],
            'before_images.*'       => ['image','mimes:jpg,jpeg,png','mimetypes:image/jpg,image/jpeg,image/png','max:5120'],
            'after_images'          => ['nullable','array'],
            'after_images.*'        => ['image','mimes:jpg,jpeg,png','mimetypes:image/jpg,image/jpeg,image/png','max:5120'],

            'description' => 'required|array|min:1',
            'description.*' => 'required|string|max:1000',

            'materials_used' => 'required|array|min:1',
            'materials_used.*' => 'required|string|max:1000',
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

