<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'     => 'عنوان',
            'parent_id' => 'دسته‌بندی والد',
        ];
    }
}
