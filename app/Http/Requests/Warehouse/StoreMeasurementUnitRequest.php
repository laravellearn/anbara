<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'symbol'            => 'nullable|string|max:20',
            'parent_id'         => 'nullable|exists:measurement_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'  => 'عنوان',
            'symbol' => 'نماد',
        ];
    }
}
