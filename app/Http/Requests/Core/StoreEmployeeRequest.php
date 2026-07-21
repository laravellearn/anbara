<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'organizational_unit_id' => 'nullable|exists:organizational_units,id',
            'employee_code'          => 'nullable|string|max:50|unique:employees,employee_code',
            'name'                   => 'required|string|max:255',
            'national_code'          => 'nullable|string|max:20',
            'mobile'                 => 'nullable|string|max:20',
            'phone'                  => 'nullable|string|max:20',
            'email'                  => 'nullable|email|max:255',
            'position'               => 'nullable|string|max:255',
            'employment_date'        => 'nullable|date',
            'address'                => 'nullable|string',
            'description'            => 'nullable|string',
            'is_active'              => 'boolean',
            'create_user'            => 'boolean',
            'username'               => 'required_if:create_user,1|string|max:50|unique:users,username',
            'password'               => 'required_if:create_user,1|string|min:6',
            'role_id'                => 'required_if:create_user,1|exists:roles,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'نام کارمند',
            'username' => 'نام کاربری',
            'password' => 'رمز عبور',
            'role_id'  => 'نقش',
        ];
    }
}
