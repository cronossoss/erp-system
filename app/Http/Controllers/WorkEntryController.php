<!-- // app/Http/Controllers/WorkEntryController.php -->

<?php

use App\Models\WorkEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class WorkEntryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_entry_type_id' => 'required|exists:work_entry_types,id',
            'date' => 'required|date',
            'time_from' => 'required',
            'time_to' => 'nullable'
        ]);

        $minutes = null;

        if ($request->time_to) {
            $minutes = Carbon::parse($request->time_from)
                ->diffInMinutes(Carbon::parse($request->time_to));
        }

        WorkEntry::create([
            'employee_id' => $request->employee_id,
            'work_entry_type_id' => $request->work_entry_type_id,
            'date' => $request->date,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'note' => $request->note,
        ]);

        return response()->json(['success' => true]);
    }
}