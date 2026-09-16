<?php

namespace App\Http\Requests;

use App\Models\Building;
use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Building::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'total_units' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'وارد کردن نام ساختمان الزامی است.',
            'name.string' => 'نام ساختمان باید متن باشد.',
            'name.max' => 'نام ساختمان نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',

            'address.string' => 'آدرس باید متن باشد.',

            'total_units.integer' => 'تعداد واحدها باید عدد باشد.',
            'total_units.min' => 'تعداد واحدها باید حداقل ۱ باشد.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام ساختمان',
            'address' => 'آدرس',
            'total_units' => 'تعداد واحدها',
        ];
    }
}
