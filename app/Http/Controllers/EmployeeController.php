<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Employee::with('organizationalUnit');

        if ($request->unit) {
            $query->where('organizational_unit_id', $request->unit);
        }

        $employees = $query->paginate(10);
        $units = \App\Models\OrganizationalUnit::all();

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
                    'employee_number' => $e->employee_number,
                    'first_name' => $e->first_name ?? '',
                    'last_name' => $e->last_name ?? '',
                    'position' => $e->position ?? '',
                    'organizational_unit_name' => optional($e->organizationalUnit)->name ?? '',
                    'contract_type' => $e->contract_type,
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


    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);

        $emp->update($request->all());

        return response()->json(['success' => true]);
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

    public function byUnit($id)
    {
        $employees = \App\Models\Employee::with('organizationalUnit')
            ->where('organizational_unit_id', $id)
            ->get();

        return response()->json($employees->map(function ($e) {
            return [
                'id' => $e->id,
                'employee_number' => $e->employee_number,
                'first_name' => $e->first_name,
                'last_name' => $e->last_name,
                'position' => $e->position,
                'organizational_unit_name' => optional($e->organizationalUnit)->name,
                'organizational_unit_id' => $e->organizational_unit_id,
                'contract_type' => $e->contract_type
            ];
        }));
    }

    public function showJson($id)
    {
        $e = \App\Models\Employee::with('organizationalUnit')->findOrFail($id);

        return response()->json([
            'id' => $e->id,
            'employee_number' => $e->employee_number,
            'first_name' => $e->first_name,
            'last_name' => $e->last_name,
            'position' => $e->position,
            'contract_type' => $e->contract_type,
            'organizational_unit' => optional($e->organizationalUnit)->name,
            'organizational_unit_id' => $e->organizational_unit_id,

            'birth_date' => $e->birth_date,
            'jmbg' => $e->jmbg,
            'employment_date' => $e->employment_date,
            'contract_end_date' => $e->contract_end_date,

            'email' => $e->email,
            'phone_work' => $e->phone_work,
            'phone_private' => $e->phone_private,

            'photo' => $e->photo
        ]);
    }
}
