<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    public function checkIn($employeeId)
    {
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'date' => $today
            ],
            [
                'check_in' => now()
            ]
        );

        // ako već postoji a nema check_in
        if (!$attendance->check_in) {
            $attendance->check_in = now();
            $attendance->save();
        }

        return $attendance;
    }

    public function checkOut($employeeId)
    {
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            throw new \Exception('Nema check-in!');
        }

        $attendance->check_out = now();

        $minutes = (int) round(
            Carbon::parse($attendance->check_in)
                ->diffInMinutes($attendance->check_out)
        );

        $attendance->worked_minutes = $minutes;

        $attendance->save();

        return $attendance;
    }
}
