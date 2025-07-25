<?php

namespace Modules\UsersAndTeams\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'          => ['nullable', 'string','unique:teams','max:255'],
            'description'   => ['nullable', 'string','max:1000'],
            'status'        => ['in:available,busy,offline'],
            //'user_id'       => ['nullable', 'integer','exists:users,id'],
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
            'name.max'              => 'The length of the name may not be more than 255 characters.',
            'name.unique'           => 'The name must be unique and not duplicate. Please use another name',
            'description.max'       => 'The length of the name may not be more than 1000 characters.',
            'status.in'             => 'The status must be one of (active,inactive,damaged,under_repair)',
            //'user_id.exists'        => 'The member you are trying to add does not exist.',
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
