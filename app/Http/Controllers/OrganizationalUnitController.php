<?php

// app/Http/Controllers/OrganizationalUnitController.php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ContractType;
use App\Models\OrganizationalGroup;



class OrganizationalUnitController extends Controller
{
    public function index()
    {

        $units = OrganizationalUnit::with(['children', 'employees'])
            ->withCount('employees')
            ->whereNull('parent_id')
            ->get();

        $allUnits = OrganizationalUnit::all();
        $groups = OrganizationalGroup::all();

        $contractTypes = ContractType::all(); // 👈 OVO FALI

        return view('organizational-units.index', compact('units', 'allUnits', 'contractTypes', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|digits:3|unique:organizational_units,code',
            'parent_id' => 'nullable|exists:organizational_units,id'
        ], [
            'name.required' => 'Naziv je obavezan',
            'code.required' => 'Šifra je obavezna',
            'code.digits' => 'Šifra mora imati tačno 3 cifre',
            'code.unique' => 'Šifra već postoji'
        ]);

        $unit = OrganizationalUnit::create($validated);

        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $unit = OrganizationalUnit::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'digits:3',
                Rule::unique('organizational_units', 'code')->ignore($unit->id)
            ],
            'parent_id' => 'nullable|exists:organizational_units,id'
        ], [
            'name.required' => 'Naziv je obavezan',
            'code.required' => 'Šifra je obavezna',
            'code.digits' => 'Šifra mora imati tačno 3 cifre',
            'code.unique' => 'Šifra već postoji'
        ]);

        $unit->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $unit = OrganizationalUnit::with('employees')->findOrFail($id);

        // 🚫 AKO IMA ZAPOSLENE → STOP
        if ($unit->employees()->count() > 0) {
            return response()->json([
                'error' => 'Ne može se obrisati jedinica koja ima zaposlene'
            ], 400);
        }

        // 🚫 AKO IMA CHILDREN → STOP (bonus zaštita)
        if ($unit->children()->count() > 0) {
            return response()->json([
                'error' => 'Ne može se obrisati jedinica koja ima podjedinice'
            ], 400);
        }

        $unit->delete();

        return response()->json(['success' => true]);
    }

    public function overview()
    {
        $groups = \App\Models\OrganizationalGroup::with([
            'units' => function ($q) {
                $q->withCount('employees');
            }
        ])->get();

        return view('organizational-units.overview', compact('groups'));
    }
}
