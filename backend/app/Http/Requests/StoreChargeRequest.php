<?php

namespace App\Http\Requests;

use App\Models\Charge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Charge::class, $this->route('building')]) ?? false;
    }

    public function rules(): array
    {
        return ['apartment_id' => ['required', Rule::exists('apartments', 'id')->where('building_id', $this->route('building')?->id)], 'title' => ['required', 'string', 'max:150'], 'month' => ['required', 'date_format:Y-m'], 'amount' => ['required', 'numeric', 'min:0.01'], 'status' => ['sometimes', Rule::in(['pending', 'paid', 'overdue'])], 'paid_at' => ['nullable', 'date', 'required_if:status,paid']];
    }

    public function messages(): array
    {
        return ['apartment_id.required' => 'انتخاب واحد الزامی است.', 'apartment_id.exists' => 'واحد انتخاب‌شده متعلق به این ساختمان نیست.', 'title.required' => 'وارد کردن عنوان شارژ الزامی است.', 'title.max' => 'عنوان شارژ نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.', 'month.required' => 'وارد کردن ماه شارژ الزامی است.', 'month.date_format' => 'ماه شارژ باید با قالب سال-ماه وارد شود.', 'amount.required' => 'وارد کردن مبلغ الزامی است.', 'amount.numeric' => 'مبلغ باید عدد باشد.', 'amount.min' => 'مبلغ باید بیشتر از صفر باشد.', 'status.in' => 'وضعیت شارژ معتبر نیست.', 'paid_at.date' => 'تاریخ پرداخت معتبر نیست.', 'paid_at.required_if' => 'برای شارژ پرداخت‌شده، تاریخ پرداخت الزامی است.'];
    }

    public function attributes(): array
    {
        return ['apartment_id' => 'واحد', 'title' => 'عنوان', 'month' => 'ماه', 'amount' => 'مبلغ', 'status' => 'وضعیت', 'paid_at' => 'تاریخ پرداخت'];
    }
}
