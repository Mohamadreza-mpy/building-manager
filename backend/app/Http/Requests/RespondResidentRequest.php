<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('residentRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['processing', 'completed', 'rejected'])], 'response' => ['nullable', 'string', 'required_if:status,completed,rejected']];
    }

    public function messages(): array
    {
        return ['status.required' => 'انتخاب وضعیت الزامی است.', 'status.in' => 'وضعیت درخواست معتبر نیست.', 'response.required_if' => 'برای این وضعیت، پاسخ مدیر الزامی است.'];
    }

    public function attributes(): array
    {
        return ['status' => 'وضعیت', 'response' => 'پاسخ'];
    }
}
