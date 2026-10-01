<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('apartment')) ?? false;
    }

    public function rules(): array
    {
        $apartment = $this->route('apartment');
        $ownerRule = Rule::exists('users', 'id')->where('role', 'owner');

        if ($this->user()?->role === 'manager') {
            $ownerRule->where('created_by', $this->user()->id);
        }

        return [
            'owner_id' => ['sometimes', 'nullable', $ownerRule],
            'resident_id' => ['sometimes', 'nullable', Rule::exists('users', 'id')->where('role', 'resident')],
            'number' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('apartments')->where('building_id', $apartment?->building_id)->ignore($apartment)],
            'floor' => ['sometimes', 'nullable', 'integer'],
            'area' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return (new StoreApartmentRequest)->messages();
    }

    public function attributes(): array
    {
        return (new StoreApartmentRequest)->attributes();
    }
}
