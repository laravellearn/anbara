<?php

namespace App\Http\Requests\Core;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationalUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $unit     = $this->route('organizational_unit');

        return [
            'name'            => 'required|string|max:255',
            'code'            => [
                'nullable', 'string', 'max:50',
                Rule::unique('organizational_units', 'code')
                    ->ignore($unit->id)
                    ->where('tenant_id', $tenantId),
            ],
            'parent_id'       => 'nullable|exists:organizational_units,id',
            'manager_user_id' => 'nullable|exists:users,id',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام واحد', 'code' => 'کد'];
    }
}
