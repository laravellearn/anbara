<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:text,number,select',
            'options'   => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام ویژگی',
            'type' => 'نوع',
        ];
    }
}
