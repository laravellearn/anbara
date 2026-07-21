<?php

namespace App\Http\Requests\Warehouse;

use App\Models\WarehouseDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'warehouse_id'                => ['required', 'integer', 'exists:warehouses,id'],
            'destination_warehouse_id'    => [
                'required_if:type,transfer', 'nullable', 'integer',
                'exists:warehouses,id', 'different:warehouse_id'
            ],
            'warehouse_location_id'       => ['nullable', 'integer', 'exists:warehouse_locations,id'],
            'contact_id'                  => ['nullable', 'integer', 'exists:contacts,id'],
            'fiscal_year_id'              => ['nullable', 'integer', 'exists:fiscal_years,id'],
            'cost_center_id'              => ['nullable', 'integer', 'exists:cost_centers,id'],
            'document_date'               => ['required', 'date'],
            'reference_number'            => ['nullable', 'string', 'max:100'],
            'description'                 => ['nullable', 'string'],
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.product_id'          => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'            => ['required', 'numeric'],
            'items.*.unit_price'          => ['nullable', 'numeric', 'min:0'],
            'items.*.measurement_unit_id' => ['nullable', 'integer', 'exists:measurement_units,id'],
            'items.*.warehouse_location_id' => ['nullable', 'integer', 'exists:warehouse_locations,id'],
            'items.*.serial_number'       => ['nullable', 'string', 'max:100'],
            'items.*.batch_number'        => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date'         => ['nullable', 'date'],
            'items.*.notes'               => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_warehouse_id.required_if' => 'برای انتقال کالا باید انبار مقصد انتخاب شود.',
            'destination_warehouse_id.different'   => 'انبار مبدأ و مقصد نمی‌توانند یکسان باشند.',
            'items.min'                            => 'حداقل یک ردیف کالا باید وارد شود.',
        ];
    }
}
