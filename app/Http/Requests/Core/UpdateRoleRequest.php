<?php

namespace App\Http\Requests\Core;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $roleId   = $this->route('role');   // int یا model

        $id = is_object($roleId) ? $roleId->id : $roleId;

        return [
            'code'          => 'required|string|unique:roles,code,' . $id . ',id,tenant_id,' . $tenantId,
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
