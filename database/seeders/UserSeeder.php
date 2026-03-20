<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password')
            ]
        );

        $employee = Employee::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'position' => 'Administrator'
            ]
        );
    }
}
