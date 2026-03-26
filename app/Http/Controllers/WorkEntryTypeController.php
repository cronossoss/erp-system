<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkEntryType;

class WorkEntryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = WorkEntryType::orderBy('code')->get();

        return view('work_entry_types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('work_entry_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        WorkEntryType::create([
            'code' => $request->code,
            'name' => $request->name,
            'is_paid' => $request->has('is_paid'),
            'counts_as_work' => false,
            'affects_vacation' => false,
        ]);

        return redirect()->route('work-entry-types.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkEntryType $workEntryType)
    {
        return view('work_entry_types.edit', compact('workEntryType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkEntryType $workEntryType)
    {
        $workEntryType->update([
            'code' => $request->code,
            'name' => $request->name,
            'is_paid' => $request->has('is_paid'),
            'counts_as_work' => $request->has('counts_as_work'),
            'affects_vacation' => $request->has('affects_vacation'),
        ]);

        return redirect()->route('work-entry-types.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkEntryType $workEntryType)
    {
        $workEntryType->delete();

        return redirect()->route('work-entry-types.index');
    }
}
