<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrganizationalUnit;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'usersCount' => User::count(),
            'organizationalUnitsCount' => OrganizationalUnit::count(),
        ]);
    }
}
