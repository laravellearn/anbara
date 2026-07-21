<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user');
        $id     = is_object($userId) ? $userId->id : $userId;

        return [
            'name'              => 'required|string|max:255',
            'mobile'            => ['required', Rule::unique('users')->ignore($id), 'regex:/^09[0-9]{9}$/'],
            'email'             => ['nullable', 'email', Rule::unique('users')->ignore($id)],
            'password'          => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'companies'         => 'required|array|min:1',
            'companies.*'       => 'exists:companies,id',
            'default_company'   => 'required',
            'company_roles'     => 'nullable|array',
            'company_roles.*'   => 'array',
            'company_roles.*.*' => 'exists:roles,id',
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
