<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class SubmitChargeReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submitReceipt', $this->route('charge')) ?? false;
    }

    public function rules(): array
    {
        return ['image' => ['required', File::types(['jpg', 'jpeg', 'png', 'heic'])->max(1024)]];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'انتخاب تصویر رسید الزامی است.',
            'image.mimes' => 'فرمت رسید فقط باید JPG، PNG یا HEIC باشد.',
            'image.max' => 'حجم تصویر رسید باید کمتر از ۱ مگابایت باشد.',
        ];
    }
}
