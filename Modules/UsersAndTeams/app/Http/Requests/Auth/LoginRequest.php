<?php

namespace Modules\UsersAndTeams\Http\Requests\Auth;

use App\Facades\Logger;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'max:255',
                'email:rfc,dns',
                'exists:users,email',
            ],
            'password' => 'required|max:128'
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'E-mail',
            'password' => 'Password',
        ];
    }

    /**
     *  Get the error messages for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function messages()
    {
        return [
            'exists' => ':attribute does not exist',
            'required' => 'the :attribute require to login, please enter the :attribute!',
        ];
    }

    /**
     * if the validation failed it return a json response
     */
    protected function failedValidation(Validator $validator)
    {
        Logger::failedValidation(
            'login-validation-check',
            'Failed Validation',
            [
                'inputs' => $this->except(['password'])
            ]
        );

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed Validate Data',
            'errors' => $validator->errors(),
        ], 422));
    }
}
