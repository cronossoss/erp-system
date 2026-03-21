<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function create(array $data): Employee
    {
        $employee = Employee::create($data);
        $employee->load('organizationalUnit');

        return $employee;
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        $employee->load('organizationalUnit');

        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
