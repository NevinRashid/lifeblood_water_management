<?php

namespace Modules\WaterSources\Http\Requests\TestingParameter;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestingParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:testing_parameters,name',
            'minimum_level' => 'nullable|numeric',
            'maximum_level' => 'nullable|numeric|gt:minimum_level',
        ];
    }
}
