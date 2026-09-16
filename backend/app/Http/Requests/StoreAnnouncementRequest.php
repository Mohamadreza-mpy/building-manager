<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Announcement::class, $this->route('building')]) ?? false;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:150'], 'body' => ['required', 'string']];
    }

    public function messages(): array
    {
        return ['title.required' => 'وارد کردن عنوان اطلاعیه الزامی است.', 'title.max' => 'عنوان اطلاعیه نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.', 'body.required' => 'وارد کردن متن اطلاعیه الزامی است.'];
    }

    public function attributes(): array
    {
        return ['title' => 'عنوان', 'body' => 'متن اطلاعیه'];
    }
}
