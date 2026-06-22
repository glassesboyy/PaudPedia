<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\SchoolMember;
use Illuminate\Support\Facades\Hash;

class ZulAcademySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolId = 4; // Zul Academy

        // Make sure the school exists
        $school = School::find($schoolId);
        if (!$school) {
            $this->command->error("School with ID $schoolId not found. Please make sure Zul Academy exists.");
            return;
        }

        $this->command->info("Seeding data for Zul Academy...");

        // 1. Create Teacher
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@zulacademy.sch.id'],
            [
                'name' => 'Guru Zul',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $teacherUser->assignRole('teacher');

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id, 'school_id' => $schoolId],
            [
                'nip' => '198001012010012001',
                'specialization' => 'Konseling Anak',
                'bio' => 'Guru berpengalaman di bidang PAUD',
            ]
        );

        // Add to school members
        SchoolMember::firstOrCreate([
            'school_id' => $schoolId,
            'user_id' => $teacherUser->id,
        ], [
            'role_type' => 'teacher',
            'is_active' => true,
        ]);

        $this->command->info("Created Teacher: Guru Zul (teacher@zulacademy.sch.id)");

        // 2. Create ClassRoom
        $class = ClassRoom::firstOrCreate(
            [
                'school_id' => $schoolId,
                'name' => 'Kelas Matahari',
                'academic_year' => '2024/2025',
            ],
            [
                'homeroom_teacher_id' => $teacher->id,
                'level' => 'Kelompok Bermain (KB)',
                'capacity' => 20,
            ]
        );

        $this->command->info("Created Class: Kelas Matahari");

        // 3. Create Parent
        $parentUser = User::firstOrCreate(
            ['email' => 'parent1@gmail.com'],
            [
                'name' => 'Orang Tua Wyasana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $parentUser->assignRole('parent');

        $parent = ParentProfile::firstOrCreate(
            ['user_id' => $parentUser->id, 'school_id' => $schoolId],
            [
                'email' => 'parent1@gmail.com',
                'father_name' => 'Zulhan Maharsa',
                'mother_name' => 'Berlian Cinta',
                'phone' => '085732324561',
                'father_occupation' => 'PNS',
                'mother_occupation' => 'IRT',
                'address' => 'Jalan Salak, Nomor 126, Kota Magelang',
            ]
        );

        // Add Parent to School Members
        SchoolMember::firstOrCreate([
            'school_id' => $schoolId,
            'user_id' => $parentUser->id,
        ], [
            'role_type' => 'parent',
            'is_active' => true,
        ]);

        $this->command->info("Created Parent: Orang Tua Wyasana (parent1@gmail.com)");

        // 4. Create Students
        $students = [
            [
                'name' => 'Wyasana Aji',
                'nisn' => '3219381209',
                'gender' => 'male',
                'birth_date' => '2023-07-01',
                'address' => 'Karanganyar',
            ],
            [
                'name' => 'Siti Aminah',
                'nisn' => '3219381210',
                'gender' => 'female',
                'birth_date' => '2023-08-15',
                'address' => 'Jalan Pahlawan 4',
            ]
        ];

        foreach ($students as $index => $studentData) {
            Student::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'nisn' => $studentData['nisn']
                ],
                [
                    'class_id' => $class->id,
                    'parent_profile_id' => $index === 0 ? $parent->id : $parent->id, // Just link to same parent for demo
                    'name' => $studentData['name'],
                    'gender' => $studentData['gender'],
                    'birth_date' => $studentData['birth_date'],
                    'address' => $studentData['address'],
                    'enrollment_date' => '2024-07-01',
                    'status' => 'active',
                ]
            );
        }

        $this->command->info("Created 2 Students linked to Parent.");
        $this->command->info("Zul Academy seeder completed successfully!");
    }
}
