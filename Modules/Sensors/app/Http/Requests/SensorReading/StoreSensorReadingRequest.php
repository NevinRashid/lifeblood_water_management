<?php

namespace Modules\Sensors\Http\Requests\SensorReading;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSensorReadingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'value' => [
                'required',
                'numeric',
                'between:-99999999999.9999,99999999999.9999' // Matches decimal(15,4)
            ],
            'unit' => 'nullable|string|max:50',
            'recorded_at' => 'required|date',
            'sensor_id' => [
                'required',
                'exists:sensors,id'
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert empty string to null for unit field
        if ($this->has('unit') && $this->unit === '') {
            $this->merge(['unit' => null]);
        }
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

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
