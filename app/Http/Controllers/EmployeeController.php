<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('organizationalUnit');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%");
            });
        }

        $employees = $query->paginate(10)->withQueryString();
        $units = OrganizationalUnit::all();

        return view('employees.index', compact('employees', 'units'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');

        $query = Employee::with('organizationalUnit');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%");
            });
        }

        $employees = $query->get();

        return response()->json(
            $employees->map(function ($e) {
                return [
                    'id' => $e->id,
                    'first_name' => $e->first_name ?? '',
                    'last_name' => $e->last_name ?? '',
                    'position' => $e->position ?? '',
                    'organizational_unit_name' => optional($e->organizationalUnit)->name ?? '',
                    'organizational_unit_id' => $e->organizational_unit_id ?? ''
                ];
            })->values()
        );
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->service->create($request->validated());

        return response()->json([
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'organizational_unit_name' => optional($employee->organizationalUnit)->name,
            'organizational_unit_id' => $employee->organizational_unit_id,
            'contract_type' => $employee->contract_type
        ]);
    }


    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee = $this->service->update($employee, $request->validated());

        return response()->json([
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'organizational_unit_name' => optional($employee->organizationalUnit)->name,
            'organizational_unit_id' => $employee->organizational_unit_id,
            'contract_type' => $employee->contract_type
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        $this->service->delete($employee);

        return response()->json(['success' => true]);
    }

    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }
}
