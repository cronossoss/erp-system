<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kasnije ćemo ubaciti role
    }

    public function rules(): array
    {
        return [
            'employee_number' => ['required', 'digits:5', 'unique:employees,employee_number'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'contract_type' => ['required', 'string'],
        ];
    }
}
