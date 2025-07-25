<?php

namespace Modules\UsersAndTeams\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => ['required','string','max:255'],
            'phone'     => ['nullable','string','max:50'],
            'address'   => ['nullable','string','max:255'],
            'email'     => ['required','email','unique:users','max:255'],
            'password'  => ['required','string','min:8','max:16','confirmed',
                                Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                            ],
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
            'name.required'            => 'The name is required please',
            'name.max'                 => 'The length of the name may not be more than 255 characters',
            'phone.max'                => 'The length of the phone may not be more than 50 characters',
            'address.max'              => 'The length of the address may not be more than 255 characters',
            'email.required'           => 'The Email is required please',
            'email.email'              => 'Please adhere to the email format (example@gmail.com)',
            'email.unique'             => 'The email must be unique and not duplicate. Please use another email',
            'email.max'                => 'The length of the email may not be more than 255 characters',
            'password.required'        => 'The password is required please',
            'password.min'             => 'Password must be at least 8 characters long',
            'password.max'             => 'The length of the password may not be more than 16 characters',
            'password.confirmed'       => 'Password must be confirmed',
            'password.letters'         => 'Password must contain at least one character',
            'password.mixed'           => 'Password must contain at least one uppercase letter and one lowercase letter',
            'password.numbers'         => 'Password must contain at least one number',
            'password.symbols'         => 'Password must contain at least one character',
            'password.uncompromised'   => 'You should choose a more secure password',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => ' Data verification error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
