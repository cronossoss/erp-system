<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceService;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return back()->with('error', 'Nema povezanog zaposlenog!');
        }

        $employee = $user->employee;

        $this->attendanceService->checkIn($employee->id);

        return back()->with('success', 'Uspešan check-in');
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return back()->with('error', 'Nema povezanog zaposlenog!');
        }

        $employee = $user->employee;

        try {
            $this->attendanceService->checkOut($employee->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Uspešan check-out');
    }
    public function status()
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return null;
        }

        $employee = $user->employee;

        return Attendance::where('employee_id', $employee->id)
            ->where('date', Carbon::today())
            ->first();
    }
}
