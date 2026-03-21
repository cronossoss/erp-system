<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => [
            'required',
            'digits:5',
            'unique:employees,employee_number'
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'organizational_unit_id' => 'nullable|exists:organizational_units,id',
            'contract_type' => 'required|string',
        ]);

        $employee = Employee::create($validated);
        $employee->load('organizationalUnit');

        return response()->json([
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'organizational_unit_name' => optional($employee->organizationalUnit)->name ?? '',
            'organizational_unit_id' => $employee->organizational_unit_id
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'employee_number' => [
                'required',
                'digits:5',
                'unique:employees,employee_number,' . $employee->id
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'organizational_unit_id' => 'nullable|exists:organizational_units,id',
            'contract_type' => 'required|string',
        ]);

        $employee->update($validated);
        $employee->load('organizationalUnit');

        return response()->json([
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'organizational_unit_name' => optional($employee->organizationalUnit)->name ?? '',
            'organizational_unit_id' => $employee->organizational_unit_id
        ]);
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
