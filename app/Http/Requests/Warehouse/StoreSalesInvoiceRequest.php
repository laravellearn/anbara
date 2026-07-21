<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'invoice_date'     => 'required|date',
            'due_date'         => 'nullable|date|after_or_equal:invoice_date',
            'customer_id'      => 'nullable|integer|exists:contacts,id',
            'warehouse_id'     => 'nullable|integer|exists:warehouses,id',
            'fiscal_year_id'   => 'nullable|integer|exists:fiscal_years,id',
            'cost_center_id'   => 'nullable|integer|exists:cost_centers,id',
            'tax_percent'      => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'description'      => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:100',

            'items'                         => 'required|array|min:1',
            'items.*.product_id'            => 'required|integer|exists:products,id',
            'items.*.measurement_unit_id'   => 'nullable|integer|exists:measurement_units,id',
            'items.*.quantity'              => 'required|numeric|min:0.0001',
            'items.*.unit_price'            => 'required|numeric|min:0',
            'items.*.discount_amount'       => 'nullable|numeric|min:0',
            'items.*.description'           => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_date.required' => 'تاریخ فاکتور الزامی است.',
            'items.required'        => 'حداقل یک ردیف کالا باید وارد شود.',
            'items.min'             => 'حداقل یک ردیف کالا باید وارد شود.',
            'items.*.product_id.required' => 'کالا در هر ردیف الزامی است.',
            'items.*.quantity.required'   => 'مقدار در هر ردیف الزامی است.',
            'items.*.unit_price.required' => 'قیمت واحد در هر ردیف الزامی است.',
        ];
    }
}
