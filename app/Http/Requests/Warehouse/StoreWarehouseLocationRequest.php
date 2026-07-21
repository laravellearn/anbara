<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'parent_id'    => 'nullable|exists:warehouse_locations,id',
            'code'         => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'type'         => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'sort_order'   => 'nullable|integer|min:0',
            'capacity'     => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'warehouse_id' => 'انبار',
            'code'         => 'کد',
            'title'        => 'عنوان',
        ];
    }
}
