<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $user  = auth()->user();
        $rules = [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'avatar_base64' => ['nullable', 'string'],
        ];

        if (! $user->employee) {
            $rules['national_code'] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return ['name' => 'نام', 'email' => 'ایمیل'];
    }
}
