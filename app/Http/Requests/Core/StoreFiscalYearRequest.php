<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class StoreFiscalYearRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'sometimes|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => 'نام سال مالی',
            'start_date' => 'تاریخ شروع',
            'end_date'   => 'تاریخ پایان',
        ];
    }
}
