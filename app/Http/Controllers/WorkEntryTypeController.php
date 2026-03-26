<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkEntryType;

class WorkEntryTypeController extends Controller
{
    public function index()
    {
        $types = WorkEntryType::orderBy('code')->get();

        return view('work_entry_types.index', compact('types'));
    }

    public function create()
    {
        return view('work_entry_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:2|unique:work_entry_types,code',
            'name' => 'required|string|max:255',
        ]);

        WorkEntryType::create([
            'code' => trim($request->code),
            'name' => $request->name,
            'is_paid' => $request->has('is_paid'),
            'counts_as_work' => $request->has('counts_as_work'),
            'affects_vacation' => $request->has('affects_vacation'),
        ]);

        return redirect()->route('work-entry-types.index');
    }

    public function edit(WorkEntryType $workEntryType)
    {
        return view('work_entry_types.edit', compact('workEntryType'));
    }

    public function update(Request $request, WorkEntryType $workEntryType)
    {
        $request->validate([
            'code' => 'required|string|max:2|unique:work_entry_types,code,' . $workEntryType->id,
            'name' => 'required|string|max:255',
        ]);

        $workEntryType->update([
            'code' => trim($request->code),
            'name' => $request->name,
            'is_paid' => $request->has('is_paid'),
            'counts_as_work' => $request->has('counts_as_work'),
            'affects_vacation' => $request->has('affects_vacation'),
        ]);

        return redirect()->route('work-entry-types.index');
    }

    public function destroy(WorkEntryType $workEntryType)
    {
        $workEntryType->delete();

        return redirect()->route('work-entry-types.index');
    }
}
