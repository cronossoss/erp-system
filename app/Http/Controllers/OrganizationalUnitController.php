<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class OrganizationalUnitController extends Controller
{
    public function index()
    {
        $units = OrganizationalUnit::with('children')
            ->whereNull('parent_id')
            ->get();

        $allUnits = OrganizationalUnit::all();

        return view('organizational-units.index', compact('units', 'allUnits'));
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
            'code' => 'required|digits:3|unique:organizational_units,code',
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
        $unit = OrganizationalUnit::findOrFail($id);
        $unit->delete();

        return response()->json(['success' => true]);
    }
}
