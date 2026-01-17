<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        // Generate attendance for last 30 days
        $startDate = now()->subDays(30);

        foreach ($students as $student) {
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                $status = fake()->randomElement([
                    AttendanceStatus::PRESENT,
                    AttendanceStatus::PRESENT,
                    AttendanceStatus::PRESENT,
                    AttendanceStatus::PRESENT,
                    AttendanceStatus::SICK,
                    AttendanceStatus::PERMISSION,
                    AttendanceStatus::ABSENT,
                ]);

                Attendance::create([
                    'student_id' => $student->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'notes' => $status !== AttendanceStatus::PRESENT ? fake()->sentence() : null,
                ]);
            }
        }
    }
}
