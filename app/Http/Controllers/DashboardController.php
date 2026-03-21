<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrganizationalUnit;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'usersCount' => User::count(),
            'employeesCount' => Employee::count(),
            'organizationalUnitsCount' => OrganizationalUnit::count(),
]);
    }
}
