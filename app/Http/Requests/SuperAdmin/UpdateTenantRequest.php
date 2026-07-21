<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenant = $this->route('tenant');

        return [
            'name'      => 'required|string|max:255',
            'slug'      => ['required', 'alpha_dash', Rule::unique('tenants', 'slug')->ignore($tenant->id)],
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
