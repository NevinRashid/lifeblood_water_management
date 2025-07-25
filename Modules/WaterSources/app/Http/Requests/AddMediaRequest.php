<?php

namespace Modules\WaterSources\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            
            'images'    => 'sometimes|required_without:documents|array',
            'images.*'  => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            'documents'   => 'sometimes|required_without:images|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ];
    }
}
