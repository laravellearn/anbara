<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'slug'      => 'required|alpha_dash|unique:tenants,slug',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام', 'slug' => 'شناسه یکتا'];
    }
}
