<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolMemberSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        
        // Get users by their roles
        $headmasters = User::role('headmaster')->get();
        $teachers = User::role('teacher')->get();
        $parents = User::role('parent')->get();

        if ($schools->isEmpty() || $headmasters->isEmpty()) {
            return;
        }

        // Assign members to each school
        foreach ($schools as $index => $school) {
            // 1. Assign Headmaster (1 per school)
            if ($headmasters->count() > $index) {
                SchoolMember::create([
                    'school_id' => $school->id,
                    'user_id' => $headmasters[$index]->id,
                    'role_type' => RoleType::HEADMASTER,
                ]);
            }

            // 2. Assign Teachers (2-3 per school)
            $teachersForSchool = $teachers->skip($index * 2)->take(2);
            foreach ($teachersForSchool as $teacher) {
                SchoolMember::create([
                    'school_id' => $school->id,
                    'user_id' => $teacher->id,
                    'role_type' => RoleType::TEACHER,
                ]);
            }

            // 3. Assign Parents (2-3 per school)
            $parentsForSchool = $parents->skip($index * 2)->take(2);
            foreach ($parentsForSchool as $parent) {
                SchoolMember::create([
                    'school_id' => $school->id,
                    'user_id' => $parent->id,
                    'role_type' => RoleType::PARENT,
                ]);
            }
        }
    }
}
