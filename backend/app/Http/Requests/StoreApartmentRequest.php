<?php

namespace App\Http\Requests;

use App\Models\Apartment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Apartment::class, $this->route('building')]) ?? false;
    }

    public function rules(): array
    {
        $buildingId = $this->route('building')?->id;

        return [
            'resident_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'resident')],
            'number' => ['required', 'string', 'max:20', Rule::unique('apartments')->where('building_id', $buildingId)],
            'floor' => ['nullable', 'integer'],
            'area' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'resident_id.exists' => 'ساکن انتخاب‌شده معتبر نیست.',
            'number.required' => 'وارد کردن شماره واحد الزامی است.',
            'number.string' => 'شماره واحد باید متن باشد.',
            'number.max' => 'شماره واحد نمی‌تواند بیشتر از ۲۰ کاراکتر باشد.',
            'number.unique' => 'این شماره واحد قبلاً در ساختمان ثبت شده است.',
            'floor.integer' => 'طبقه باید عدد صحیح باشد.',
            'area.numeric' => 'مساحت باید عدد باشد.',
            'area.min' => 'مساحت نمی‌تواند منفی باشد.',
            'area.max' => 'مساحت بیش از حد مجاز است.',
        ];
    }

    public function attributes(): array
    {
        return ['resident_id' => 'ساکن', 'number' => 'شماره واحد', 'floor' => 'طبقه', 'area' => 'مساحت'];
    }
}
