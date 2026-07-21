<?php

namespace App\Http\Requests\Warehouse;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId    = app(TenantManager::class)->getTenantId();
        $productType = $this->route('product_type');

        return [
            'title'       => [
                'required', 'string', 'max:255',
                Rule::unique('product_types', 'title')
                    ->ignore($productType->id)
                    ->where('tenant_id', $tenantId),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'attributes'  => 'nullable|array',
            'attributes.*.id'          => 'exists:product_attributes,id',
            'attributes.*.is_required' => 'boolean',
            'attributes.*.sort_order'  => 'integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'عنوان نوع کالا',
        ];
    }
}
