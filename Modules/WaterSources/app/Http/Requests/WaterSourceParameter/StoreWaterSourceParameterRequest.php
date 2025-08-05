<?php

namespace Modules\WaterSources\Http\Requests\WaterSourceParameter;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterSourceParameterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'parameters' => 'required|array|min:1',
            'parameters.*' => 'required|integer|exists:testing_parameters,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'parameters.required' => 'حقل المعايير مطلوب.',
            'parameters.array' => 'يجب أن تكون المعايير على شكل مصفوفة.',
            'parameters.*.exists' => 'أحد معرفات المعايير المحدد غير موجود.',
        ];
    }
}
