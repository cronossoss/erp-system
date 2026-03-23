<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EmployeeController extends Controller
{
    // LISTA
    public function index(Request $request)
    {
        $query = Employee::with('organizationalUnit');

        if ($request->unit) {
            $query->where('organizational_unit_id', $request->unit);
        }

        $employees = $query->paginate(10);
        $units = \App\Models\OrganizationalUnit::all();

        return view('employees.index', compact('employees', 'units'));
    }

    // SEARCH (AJAX)
    public function search(Request $request)
    {
        $search = $request->get('search');

        $query = Employee::with('organizationalUnit');

        if (!empty($search)) {

            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', [$search . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$search . '%'])
                    ->orWhereRaw('LOWER(employee_number::text) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(position) LIKE ?', ['%' . $search . '%']);
            });
        }
        $employees = $query->get();

        return response()->json($employees->map(fn($e) => [
            'id' => $e->id,
            'employee_number' => $e->employee_number,
            'first_name' => $e->first_name ?? '',
            'last_name' => $e->last_name ?? '',
            'position' => $e->position ?? '',
            'organizational_unit_name' => optional($e->organizationalUnit)->name ?? '',
            'contract_type' => $e->contract_type,
            'organizational_unit_id' => $e->organizational_unit_id ?? ''
        ]));
    }

    // CREATE
    public function store(Request $request)
    {
        // 🔒 ADMIN ZAŠTITA
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'error' => 'Nemate dozvolu'
            ], 403);
        }

        // ✅ VALIDACIJA
        if (Employee::where('employee_number', $request->employee_number)->exists()) {
            return response()->json([
                'error' => 'Matični broj već postoji'
            ], 422);
        }

        try {

            $employee = Employee::create([
                'employee_number' => $request->employee_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'position' => $request->position,
                'organizational_unit_id' => $request->organizational_unit_id,
                'contract_type' => $request->contract_type
            ]);

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
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        // 🔒 ADMIN ZAŠTITA
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'error' => 'Nemate dozvolu'
            ], 403);
        }
        $emp = Employee::findOrFail($id);

        // ✅ PROVERA DUPLIKATA (ALI IGNORIŠI SEBE)
        if (
            Employee::where('employee_number', $request->employee_number)
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json([
                'error' => 'Matični broj već postoji'
            ], 422);
        }

        try {

            $emp->update($request->only([
                'employee_number',
                'first_name',
                'last_name',
                'position',
                'contract_type',
                'organizational_unit_id',
                'employment_date',
                'contract_end_date',
                'email',
                'phone_work',
                'phone_private',
                'jmbg',
                'birth_date'
            ]));

            return response()->json(['success' => true]);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE
    public function destroy($id)
    {

        // 🔒 ADMIN ZAŠTITA
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'error' => 'Nemate dozvolu'
            ], 403);
        }
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['success' => true]);
    }

    // BY UNIT (dashboard)
    public function byUnit($id)
    {
        $employees = Employee::with('organizationalUnit')
            ->where('organizational_unit_id', $id)
            ->get();

        return response()->json($employees->map(fn($e) => [
            'id' => $e->id,
            'employee_number' => $e->employee_number,
            'first_name' => $e->first_name,
            'last_name' => $e->last_name,
            'position' => $e->position,
            'organizational_unit_name' => $e->organizationalUnit
                ? $e->organizationalUnit->code . ' - ' . $e->organizationalUnit->name
                : null,
            'organizational_unit_id' => $e->organizational_unit_id,
            'contract_type' => $e->contract_type
        ]));
    }

    // DETAIL
    public function showJson($id)
    {
        $employee = \App\Models\Employee::with('organizationalUnit')->findOrFail($id);

        return response()->json([
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'email' => $employee->email,

            'organizational_unit_name' => $employee->organizationalUnit
                ? $employee->organizationalUnit->code . ' - ' . $employee->organizationalUnit->name
                : null,

            'contract_type' => $employee->contract_type,
            'birth_date' => $employee->birth_date,
            'jmbg' => $employee->jmbg,
            'employment_date' => $employee->employment_date,
            'contract_end_date' => $employee->contract_end_date,
            'phone_work' => $employee->phone_work,
            'phone_private' => $employee->phone_private,
            'photo' => $employee->photo
        ]);
    }
}
