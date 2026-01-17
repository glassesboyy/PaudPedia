<?php

namespace Database\Seeders;

use App\Models\SchoolMember;
use App\Models\Teacher;
use App\Enums\RoleType;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherMembers = SchoolMember::where('role_type', RoleType::TEACHER)->get();

        foreach ($teacherMembers as $member) {
            Teacher::create([
                'user_id' => $member->user_id,
                'school_id' => $member->school_id,
                'nip' => 'NIP' . str_pad(fake()->unique()->numberBetween(1, 9999), 8, '0', STR_PAD_LEFT),
                'specialization' => fake()->randomElement([
                    'Seni & Musik',
                    'Matematika Dasar',
                    'Bahasa Indonesia',
                    'Pengembangan Motorik',
                    'Kreativitas Anak',
                ]),
            ]);
        }
    }
}
