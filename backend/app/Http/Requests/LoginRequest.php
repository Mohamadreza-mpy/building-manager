<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['mobile' => ['required', 'string'], 'password' => ['required', 'string']];
    }

    public function messages(): array
    {
        return ['mobile.required' => 'وارد کردن شماره موبایل الزامی است.', 'password.required' => 'وارد کردن رمز عبور الزامی است.'];
    }

    public function attributes(): array
    {
        return ['mobile' => 'شماره موبایل', 'password' => 'رمز عبور'];
    }
}
