<?php

namespace Modules\WaterSources\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestingParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:testing_parameters,name,' . $this->route('id'),
            'minimum_level' => 'nullable|numeric',
            'maximum_level' => 'nullable|numeric',
        ];
    }
}
