<?php

namespace App\Http\Requests\Warehouse;

use App\Enums\InventoryTransactionType;
use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = app(TenantManager::class)->getTenantId();

        return [
            'warehouse_id'          => ['required', Rule::exists('warehouses', 'id')->where('tenant_id', $tenantId)],
            'warehouse_location_id' => ['nullable', Rule::exists('warehouse_locations', 'id')->where('tenant_id', $tenantId)],
            'product_id'            => ['required', Rule::exists('products', 'id')->where('tenant_id', $tenantId)],
            'measurement_unit_id'   => ['nullable', Rule::exists('measurement_units', 'id')->where('tenant_id', $tenantId)],
            'fiscal_year_id'        => ['nullable', Rule::exists('fiscal_years', 'id')->where('tenant_id', $tenantId)],
            'cost_center_id'        => ['nullable', Rule::exists('cost_centers', 'id')->where('tenant_id', $tenantId)],
            'type'                  => ['required', Rule::enum(InventoryTransactionType::class)],
            'quantity'              => ['required', 'numeric', 'gt:0'],
            'unit_price'            => ['nullable', 'numeric', 'min:0'],
            'batch_number'          => ['nullable', 'string', 'max:100'],
            'serial_number'         => ['nullable', 'string', 'max:100'],
            'expiry_date'           => ['nullable', 'date'],
            'description'           => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'warehouse_id'        => 'انبار',
            'product_id'          => 'کالا',
            'type'                => 'نوع تراکنش',
            'quantity'            => 'مقدار',
            'unit_price'          => 'قیمت واحد',
            'expiry_date'         => 'تاریخ انقضا',
            'measurement_unit_id' => 'واحد اندازه‌گیری',
            'fiscal_year_id'      => 'سال مالی',
            'cost_center_id'      => 'مرکز هزینه',
        ];
    }
}
