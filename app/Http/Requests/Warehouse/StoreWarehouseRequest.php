<?php

namespace App\Http\Requests\Warehouse;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();

        return [
            'code'                 => [
                'required', 'string', 'max:50',
                Rule::unique('warehouses', 'code')->where('tenant_id', $tenantId),
            ],
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'address'              => 'nullable|string',
            'allow_negative_stock' => 'boolean',
            'is_active'            => 'boolean',
            'users'                => 'nullable|array',
            'users.*'              => 'exists:users,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'  => 'کد انبار',
            'title' => 'نام انبار',
        ];
    }
}
