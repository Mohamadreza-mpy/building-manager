<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Expense::class, $this->route('building')]) ?? false;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:150'], 'amount' => ['required', 'numeric', 'min:0.01'], 'category' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string'], 'image' => ['nullable', 'image', 'max:5120']];
    }

    public function messages(): array
    {
        return ['title.required' => 'وارد کردن عنوان هزینه الزامی است.', 'amount.required' => 'وارد کردن مبلغ الزامی است.', 'amount.numeric' => 'مبلغ باید عدد باشد.', 'amount.min' => 'مبلغ باید بیشتر از صفر باشد.', 'category.required' => 'وارد کردن دسته‌بندی الزامی است.', 'image.image' => 'رسید باید یک تصویر باشد.', 'image.max' => 'حجم تصویر رسید نباید بیشتر از ۵ مگابایت باشد.'];
    }

    public function attributes(): array
    {
        return ['title' => 'عنوان', 'amount' => 'مبلغ', 'category' => 'دسته‌بندی', 'description' => 'توضیحات', 'image' => 'تصویر رسید'];
    }
}
