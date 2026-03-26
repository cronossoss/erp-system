<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkEntryType;

class WorkEntryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $types = [
            ['code' => '01', 'name' => 'Rad', 'is_paid' => true, 'counts_as_work' => true],
            ['code' => '02', 'name' => 'Pauza', 'is_paid' => true],
            ['code' => '03', 'name' => 'Privatno', 'is_paid' => false],
            ['code' => '10', 'name' => 'Godišnji', 'is_paid' => true, 'affects_vacation' => true],
            ['code' => '11', 'name' => 'Bolovanje', 'is_paid' => true],
        ];

        foreach ($types as $type) {
            WorkEntryType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
