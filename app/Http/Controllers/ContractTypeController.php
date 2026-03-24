<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractType;

class ContractTypeController extends Controller
{
    public function index()
    {
        $types = \App\Models\ContractType::orderBy('name')->get();
        return view('contract-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        return ContractType::create([
            'name' => $request->name,
            'code' => $request->code
        ]);
    }

    public function update(Request $request, $id)
    {
        $type = \App\Models\ContractType::findOrFail($id);
        $type->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        \App\Models\ContractType::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
