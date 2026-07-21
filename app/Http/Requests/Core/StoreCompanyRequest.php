<?php

namespace App\Http\Requests\Core;

use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50',
            'type'          => 'nullable|string|max:50',
            'national_id'   => 'nullable|string|max:20',
            'economic_code' => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'parent_id'     => 'nullable|exists:companies,id',
            'is_active'     => 'boolean',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام سازمان', 'code' => 'کد', 'logo' => 'لوگو'];
    }
}
