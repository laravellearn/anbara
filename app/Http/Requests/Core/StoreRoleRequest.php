<?php

namespace App\Http\Requests\Core;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();

        return [
            'code'          => 'required|string|unique:roles,code,NULL,id,tenant_id,' . $tenantId,
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'scope'         => 'required|in:tenant,company',
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'کد نقش', 'title' => 'عنوان نقش', 'scope' => 'محدوده'];
    }
}
