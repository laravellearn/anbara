<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $plan = $this->route('plan');

        return [
            'name'          => 'required|string|max:255',
            'slug'          => ['required', Rule::unique('plans', 'slug')->ignore($plan->id)],
            'monthly_price' => 'nullable|numeric|min:0',
            'yearly_price'  => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'limits'        => 'nullable|array',
            'features'      => 'nullable|array',
            'is_active'     => 'boolean',
            'sort_order'    => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'نام پلن', 'slug' => 'شناسه یکتا'];
    }
}
