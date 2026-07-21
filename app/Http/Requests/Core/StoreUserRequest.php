<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'mobile'            => [
                'required',
                Rule::unique('users')->whereNotNull('mobile_verified_at'),
                'regex:/^09[0-9]{9}$/',
            ],
            'email'             => ['nullable', 'email', 'unique:users,email'],
            'password'          => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'companies'         => 'required|array|min:1',
            'companies.*'       => 'exists:companies,id',
            'default_company'   => 'required',
            'company_roles'     => 'nullable|array',
            'company_roles.*'   => 'array',
            'company_roles.*.*' => 'exists:roles,id',
            'create_employee'   => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'نام',
            'mobile'          => 'شماره موبایل',
            'email'           => 'ایمیل',
            'password'        => 'رمز عبور',
            'companies'       => 'شرکت‌ها',
            'default_company' => 'شرکت پیش‌فرض',
        ];
    }
}
