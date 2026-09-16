<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('charge')) ?? false;
    }

    public function rules(): array
    {
        return ['title' => ['sometimes', 'required', 'string', 'max:150'], 'amount' => ['sometimes', 'numeric', 'min:0.01'], 'status' => ['sometimes', Rule::in(['pending', 'paid', 'overdue'])], 'paid_at' => ['nullable', 'date', 'required_if:status,paid']];
    }

    public function messages(): array
    {
        return (new StoreChargeRequest)->messages();
    }

    public function attributes(): array
    {
        return (new StoreChargeRequest)->attributes();
    }
}
