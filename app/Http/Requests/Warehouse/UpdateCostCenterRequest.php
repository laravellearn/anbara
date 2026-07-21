<?php

namespace App\Http\Requests\Warehouse;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCostCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId   = app(TenantManager::class)->getTenantId();
        $costCenter = $this->route('cost_center');

        return [
            'code'        => [
                'required', 'string', 'max:50',
                Rule::unique('cost_centers', 'code')
                    ->ignore($costCenter->id)
                    ->where('tenant_id', $tenantId),
            ],
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'  => 'کد',
            'title' => 'عنوان',
        ];
    }
}
