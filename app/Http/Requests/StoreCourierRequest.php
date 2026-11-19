<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+() .-]+$/', 'unique:couriers,phone'],
            'email' => ['nullable', 'email:rfc', 'max:255', 'unique:couriers,email'],
            'address' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'integer', 'between:1,5'],
            'joined_at' => ['required', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
