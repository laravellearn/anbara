<?php

namespace App\Http\Requests\Warehouse;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('brands')->where('tenant_id', $tenantId)],
            'slug'        => ['nullable', 'string', 'max:255'],
            'logo'        => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام برند'];
    }
}
