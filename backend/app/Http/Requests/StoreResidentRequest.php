<?php

namespace App\Http\Requests;

use App\Models\ResidentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ResidentRequest::class) ?? false;
    }

    public function rules(): array
    {
        return ['apartment_id' => ['required', Rule::exists('apartments', 'id')->where('resident_id', $this->user()?->id)], 'title' => ['required', 'string', 'max:150'], 'description' => ['required', 'string']];
    }

    public function messages(): array
    {
        return ['apartment_id.required' => 'انتخاب واحد الزامی است.', 'apartment_id.exists' => 'واحد انتخاب‌شده متعلق به شما نیست.', 'title.required' => 'وارد کردن عنوان درخواست الزامی است.', 'description.required' => 'وارد کردن شرح درخواست الزامی است.'];
    }

    public function attributes(): array
    {
        return ['apartment_id' => 'واحد', 'title' => 'عنوان', 'description' => 'شرح درخواست'];
    }
}
