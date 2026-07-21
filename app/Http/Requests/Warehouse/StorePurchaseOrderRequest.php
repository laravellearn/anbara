<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id'                 => ['nullable', 'integer', 'exists:contacts,id'],
            'warehouse_id'                => ['required', 'integer', 'exists:warehouses,id'],
            'fiscal_year_id'              => ['nullable', 'integer', 'exists:fiscal_years,id'],
            'cost_center_id'              => ['nullable', 'integer', 'exists:cost_centers,id'],
            'order_date'                  => ['required', 'date'],
            'expected_delivery_date'      => ['nullable', 'date', 'after_or_equal:order_date'],
            'currency'                    => ['nullable', 'string', 'max:10'],
            'discount_percent'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_percent'                 => ['nullable', 'numeric', 'min:0', 'max:100'],
            'shipping_cost'               => ['nullable', 'numeric', 'min:0'],
            'reference_number'            => ['nullable', 'string', 'max:100'],
            'terms_and_conditions'        => ['nullable', 'string'],
            'notes'                       => ['nullable', 'string'],
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.product_id'          => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity_ordered'    => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price'          => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_percent'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.measurement_unit_id' => ['nullable', 'integer', 'exists:measurement_units,id'],
            'items.*.description'         => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.min'                         => 'حداقل یک ردیف کالا باید وارد شود.',
            'items.*.quantity_ordered.required' => 'مقدار سفارش اجباری است.',
            'items.*.product_id.required'       => 'انتخاب کالا اجباری است.',
            'expected_delivery_date.after_or_equal' => 'تاریخ تحویل باید بعد از تاریخ سفارش باشد.',
        ];
    }
}
