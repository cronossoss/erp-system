<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\OrganizationalGroup;

class OrganizationalGroupController extends Controller
{
    public function index()
    {
        $groups = OrganizationalGroup::all();
        return view('organizational-groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|digits:2|unique:organizational_groups,code'
        ]);

        return OrganizationalGroup::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $group = OrganizationalGroup::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'code' => [
                'required',
                'digits:2',
                Rule::unique('organizational_groups', 'code')->ignore($group->id)
            ]
        ]);

        $group->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $group = OrganizationalGroup::findOrFail($id);

        if ($group->units()->count() > 0) {
            return response()->json([
                'error' => 'Ne može se obrisati celina koja ima jedinice'
            ], 400);
        }

        $group->delete();

        return response()->json(['success' => true]);
    }
}
