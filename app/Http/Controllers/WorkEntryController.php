<?php

// app/Http/Controllers/WorkEntryController.php

namespace App\Http\Controllers;

use App\Models\WorkEntry;
use App\Models\WorkEntryType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EmployeeVacation;

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

        $entry = WorkEntry::create([
            'employee_id' => $request->employee_id,
            'work_entry_type_id' => $request->work_entry_type_id,
            'date' => $request->date,
            'time_from' => $request->date . ' ' . $request->time_from,
            'time_to' => $request->time_to
                ? $request->date . ' ' . $request->time_to
                : null,
            'note' => $request->note,
        ]);

        $type = $entry->type;

        $types = WorkEntryType::orderBy('code')->get();

        if ($type && $type->affects_vacation) {

            $year = date('Y', strtotime($entry->date));

            $vacation = EmployeeVacation::firstOrCreate(
                [
                    'employee_id' => $entry->employee_id,
                    'year' => $year
                ],
                [
                    'total_days' => 20
                ]
            );

            $vacation->increment('used_days');
     
        }

        return response()->json(['success' => true]);
    }
}