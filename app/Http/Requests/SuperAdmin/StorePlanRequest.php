<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'slug'          => 'required|unique:plans,slug',
            'monthly_price' => 'nullable|numeric|min:0',
            'yearly_price'  => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'limits'        => 'nullable|array',
            'features'      => 'nullable|array',
            'is_active'     => 'boolean',
            'sort_order'    => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام پلن', 'slug' => 'شناسه یکتا'];
    }
}
