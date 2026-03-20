<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;

class OrganizationalUnitController extends Controller
{
    public function index()
    {
        // 🔥 ROOT + CHILDREN (za tree)
        $units = OrganizationalUnit::with('children.children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('organizational-units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:organizational_units,id'
        ]);

        $unit = OrganizationalUnit::create($validated);

        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $unit = OrganizationalUnit::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:organizational_units,id'
        ]);

        $unit->update($validated);

        return response()->json($unit);
    }

    public function destroy($id)
    {
        OrganizationalUnit::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
