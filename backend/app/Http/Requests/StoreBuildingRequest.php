<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'total_units' => [
                'nullable',
                'integer',
                'min:1'
            ],
        ];
    }
}
