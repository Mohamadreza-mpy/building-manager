<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'mobile' => ['required', 'string', 'regex:/^09\d{9}$/', 'unique:users,mobile'], 'email' => ['nullable', 'email', 'max:150', 'unique:users,email'], 'password' => ['required', 'string', 'min:6', 'confirmed']];
    }

    public function messages(): array
    {
        return ['name.required' => 'وارد کردن نام الزامی است.', 'mobile.required' => 'وارد کردن شماره موبایل الزامی است.', 'mobile.regex' => 'شماره موبایل معتبر نیست.', 'mobile.unique' => 'این شماره موبایل قبلاً ثبت شده است.', 'email.email' => 'ایمیل معتبر نیست.', 'email.unique' => 'این ایمیل قبلاً ثبت شده است.', 'password.required' => 'وارد کردن رمز عبور الزامی است.', 'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.', 'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.'];
    }

    public function attributes(): array
    {
        return ['name' => 'نام', 'mobile' => 'شماره موبایل', 'email' => 'ایمیل', 'password' => 'رمز عبور'];
    }
}
