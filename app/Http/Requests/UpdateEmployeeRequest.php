<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('employee');

        return [
            'employee_number' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'position' => 'nullable|string',

            'email' => 'nullable|email',
            'phone_work' => 'nullable|string',
            'phone_private' => 'nullable|string',

            'birth_date' => 'nullable|date',
            'jmbg' => 'nullable|string',

            'employment_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',

            'contract_type' => 'nullable|string',
            'organizational_unit_id' => 'nullable|exists:organizational_units,id',
        ];
    }
}
