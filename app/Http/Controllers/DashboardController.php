<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrganizationalUnit;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\OrganizationalGroup;

class DashboardController extends Controller
{
    public function index()
    {
        $groupsCount = OrganizationalGroup::count();
        $units = OrganizationalUnit::withCount('employees')
            ->orderBy('code')
            ->get();

        // 🔥 PREMEŠTENO UNUTAR FUNKCIJE

        $expiring = Employee::whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', Carbon::now()->addDays(15))
            ->orderByRaw("
                CASE
                    WHEN contract_end_date < NOW() THEN 0
                    WHEN contract_end_date <= NOW() + INTERVAL '3 days' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('contract_end_date')
            ->get();

        return view('dashboard', [
            'usersCount' => User::count(),
            'employeesCount' => Employee::count(),
            'organizationalUnitsCount' => OrganizationalUnit::count(),
            'groupsCount' => $groupsCount, // 👈 FIX
            'expiringContracts' => $expiring,
            'units' => $units
        ]);
    }
}
