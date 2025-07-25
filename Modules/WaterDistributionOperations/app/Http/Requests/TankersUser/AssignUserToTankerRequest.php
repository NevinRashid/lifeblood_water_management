<?php

namespace Modules\WaterDistributionOperations\Http\Requests\TankersUser;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserToTankerRequest extends FormRequest
{
    /**
     * 
     * @return bool
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
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
