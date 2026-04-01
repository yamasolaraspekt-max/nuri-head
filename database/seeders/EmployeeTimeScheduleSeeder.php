<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeTimeSchedule;

class EmployeeTimeScheduleSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            EmployeeTimeSchedule::createDefaultWeekForEmployee($employee);
        }
    }
}
