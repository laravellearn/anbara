<?php

namespace App\Http\Requests\Warehouse;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $product  = $this->route('product');

        return [
            'product_type_id'     => 'nullable|exists:product_types,id',
            'category_id'         => 'nullable|exists:categories,id',
            'measurement_unit_id' => 'nullable|exists:measurement_units,id',
            'title'               => 'required|string|max:255',
            'sku'                 => [
                'nullable', 'string', 'max:50',
                Rule::unique('products', 'sku')
                    ->ignore($product->id)
                    ->where('tenant_id', $tenantId),
            ],
            'barcode'             => 'nullable|string|max:50',
            'model'               => 'nullable|string|max:255',
            'part_number'         => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'minimum_stock'       => 'nullable|numeric|min:0',
            'maximum_stock'       => 'nullable|numeric|min:0',
            'is_asset'            => 'boolean',
            'is_active'           => 'boolean',
            'measurement_units'   => 'nullable|array',
            'measurement_units.*.id'               => 'exists:measurement_units,id',
            'measurement_units.*.conversion_factor' => 'nullable|numeric|min:0',
            'measurement_units.*.is_default'        => 'boolean',
            'attribute_values'    => 'nullable|array',
            'attribute_values.*.attribute_id' => 'exists:product_attributes,id',
            'attribute_values.*.value'        => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'نام کالا',
            'sku'   => 'کد کالا (SKU)',
        ];
    }
}
