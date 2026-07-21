<?php

namespace App\Http\Requests\Core;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();

        return [
            'code'          => ['nullable', 'string', 'max:50', Rule::unique('contacts', 'code')->where('tenant_id', $tenantId)],
            'type'          => 'required|in:customer,supplier,both',
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'national_code' => 'nullable|string|max:20',
            'economic_code' => 'nullable|string|max:20',
            'mobile'        => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'website'       => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
            'country_id'    => 'nullable|exists:countries,id',
            'province_id'   => 'nullable|exists:provinces,id',
            'city'          => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return ['type' => 'نوع مخاطب', 'code' => 'کد'];
    }
}
