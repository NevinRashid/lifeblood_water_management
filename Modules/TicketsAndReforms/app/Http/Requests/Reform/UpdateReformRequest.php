<?php

namespace Modules\TicketsAndReforms\Http\Requests\Reform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReformRequest extends FormRequest
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
            'description'       => ['nullable','string','max:1000'],
            'materials_used'    => ['nullable','string','max:500'],
            'reform_cost'       => ['nullable','numeric'],
            'start_date'        => ['nullable','date'],
            'end_date'          => ['nullable','date'],
            'status'            => ['nullable','in:pending,in_progress,completed,failed'],
            'team_id'           => ['nullable', 'integer','exists:teams,id'],
            'trouble_ticket_id' => ['nullable', 'integer','unique:reforms','exists:trouble_tickets,id'],
            'before_images'     => ['nullable','array'],
            'before_images.*'   => ['image','mimes:jpg,jpeg,png','mimetypes:image/jpg,image/jpeg,image/png','max:5120'],
            'after_images'      => ['nullable','array'],
            'after_images.*'    => ['image','mimes:jpg,jpeg,png','mimetypes:image/jpg,image/jpeg,image/png','max:5120'],
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
            'description.max'             => 'The length of the description may not be more than 1000 characters.',
            'materials_used.max'          => 'The length of the materials_used may not be more than 500 characters.',
            'reform_cost.numeric'         => 'The reform_cost must be a numeric value.',
            'start_date.date'             => 'The start_date must be a date.',
            'end_date.date'               => 'The end_date must be a date.',
            'status.in'                   => 'The status must be one of (pending,in_progress,completed,failed)',
            'team_id.exists'              => 'The team does not exist.',
            'trouble_ticket_id.exists'    => 'The troubleTicket does not exist.',
            'before_images.array'         => 'The before images must be an array.',
            'before_images.*.image'       => 'The before_images must be an image',
            'before_images.*.mimes'       => 'The before_images must be a file of type: jpg,jpeg,png',
            'before_images.*.max'         => 'The before_images size must not exceed 5 MB',
            'after_images.array'          => 'The before images must be an array.',
            'after_images.*.image'        => 'The after_images must be an image',
            'after_images.*.mimes'        => 'The after_images must be a file of type: jpg,jpeg,png',
            'after_images.*.max'          => 'The after_images size must not exceed 5 MB',
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
