<?php

namespace Modules\UsersAndTeams\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTeamRequest extends FormRequest
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
            'name'          => ['required', 'string','unique:teams','max:255'],
            'description'   => ['required', 'string','max:1000'],
            'status'        => ['in:available,busy,offline'],
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
            'name.required'         => 'The name is required please.',
            'name.max'              => 'The length of the name may not be more than 255 characters.',
            'name.unique'           => 'The name must be unique and not duplicate. Please use another name',
            'description.required'  => 'The description is required please.',
            'description.max'       => 'The length of the description may not be more than 1000 characters.',
            'status.in'             => 'The status must be one of (active,inactive,damaged,under_repair)',
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
