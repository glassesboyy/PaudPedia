<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\ParentProfile;
use App\Models\SchoolMember;
use Illuminate\Database\Seeder;

class ParentProfileSeeder extends Seeder
{
    public function run(): void
    {
        $parentMembers = SchoolMember::where('role_type', RoleType::PARENT)->get();

        foreach ($parentMembers as $member) {

            // Hindari duplicate (school_id + user_id)
            if (
                ParentProfile::where('school_id', $member->school_id)
                    ->where('user_id', $member->user_id)
                    ->exists()
            ) {
                continue;
            }

            ParentProfile::create([
                'school_id' => $member->school_id,
                'user_id'   => $member->user_id,
                'email'     => fake()->unique()->safeEmail(),
                'phone'     => '08' . fake()->numerify('##########'),

                'father_name' => fake()->name('male'),
                'father_occupation' => fake()->randomElement([
                    'Pegawai Swasta',
                    'PNS',
                    'Wiraswasta',
                    'Dokter',
                    'Guru',
                ]),

                'mother_name' => fake()->name('female'),
                'mother_occupation' => fake()->randomElement([
                    'Ibu Rumah Tangga',
                    'Pegawai Swasta',
                    'PNS',
                    'Wiraswasta',
                    'Guru',
                ]),

                'address' => fake()->address(),
            ]);
        }
    }
}
