<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;

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
        $unit = OrganizationalUnit::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $unit = OrganizationalUnit::findOrFail($id);

        $unit->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return response()->json($unit);
    }

    public function destroy($id)
    {
        $unit = OrganizationalUnit::findOrFail($id);
        $unit->delete();

        return response()->json(['success' => true]);
    }
}
