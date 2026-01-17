<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create all roles based on USE_CASE.md
        $roles = [
            'admin' => Role::firstOrCreate(['name' => 'admin']),           // Super Admin - Full access
            'moderator' => Role::firstOrCreate(['name' => 'moderator']),   // Content Manager
            'headmaster' => Role::firstOrCreate(['name' => 'headmaster']), // Kepala Sekolah
            'teacher' => Role::firstOrCreate(['name' => 'teacher']),       // Guru
            'parent' => Role::firstOrCreate(['name' => 'parent']),         // Orang Tua
            'user' => Role::firstOrCreate(['name' => 'user']),             // User terdaftar (e-learning)
        ];

        // 1. ADMIN (Super Admin) - Full system access
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@paudpedia.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'gender' => Gender::MALE,
            'date_of_birth' => '1985-01-15',
            'address' => 'Jakarta Pusat',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($roles['admin']);

        // 2. MODERATOR (Content Manager) - Manage content (webinar, course, product, artikel)
        $moderator = User::create([
            'name' => 'Content Moderator',
            'email' => 'moderator@paudpedia.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'gender' => Gender::FEMALE,
            'date_of_birth' => '1990-05-20',
            'address' => 'Bandung',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $moderator->assignRole($roles['moderator']);

        // 3. HEADMASTER (Kepala Sekolah) - School owner, akan di-assign ke school nanti
        $headmasters = [
            [
                'name' => 'Drs. Bambang Sutrisno, M.Pd',
                'email' => 'headmaster1@test.com',
                'phone' => '081234567892',
                'gender' => Gender::MALE,
                'address' => 'Jakarta Selatan',
            ],
            [
                'name' => 'Hj. Siti Nurhaliza, S.Pd',
                'email' => 'headmaster2@test.com',
                'phone' => '081234567893',
                'gender' => Gender::FEMALE,
                'address' => 'Bogor',
            ],
            [
                'name' => 'Ahmad Fauzi, S.Pd.I',
                'email' => 'headmaster3@test.com',
                'phone' => '081234567894',
                'gender' => Gender::MALE,
                'address' => 'Bandung',
            ],
        ];

        foreach ($headmasters as $headmasterData) {
            $headmaster = User::create([
                ...$headmasterData,
                'password' => Hash::make('password'),
                'date_of_birth' => fake()->dateTimeBetween('-55 years', '-40 years')->format('Y-m-d'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $headmaster->assignRole($roles['headmaster']);
        }

        // 4. TEACHER (Guru) - Teaching activities (absensi, nilai)
        $teachers = [
            [
                'name' => 'Dewi Kusuma, S.Pd',
                'email' => 'teacher1@test.com',
                'phone' => '081234567895',
                'gender' => Gender::FEMALE,
            ],
            [
                'name' => 'Rina Puspita, S.Pd',
                'email' => 'teacher2@test.com',
                'phone' => '081234567896',
                'gender' => Gender::FEMALE,
            ],
            [
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'teacher3@test.com',
                'phone' => '081234567897',
                'gender' => Gender::MALE,
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacher = User::create([
                ...$teacherData,
                'password' => Hash::make('password'),
                'date_of_birth' => fake()->dateTimeBetween('-45 years', '-25 years')->format('Y-m-d'),
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $teacher->assignRole($roles['teacher']);
        }

        // 5. PARENT (Orang Tua) - Monitor children (PAUD usia 0-6 tahun)
        $parents = [
            [
                'name' => 'Ibu Ani Wijaya',
                'email' => 'parent1@test.com',
                'phone' => '081234567898',
                'gender' => Gender::FEMALE,
            ],
            [
                'name' => 'Bapak Joko Prasetyo',
                'email' => 'parent2@test.com',
                'phone' => '081234567899',
                'gender' => Gender::MALE,
            ],
            [
                'name' => 'Ibu Fitri Handayani',
                'email' => 'parent3@test.com',
                'phone' => '081234567800',
                'gender' => Gender::FEMALE,
            ],
        ];

        foreach ($parents as $parentData) {
            $parent = User::create([
                ...$parentData,
                'password' => Hash::make('password'),
                'date_of_birth' => fake()->dateTimeBetween('-45 years', '-25 years')->format('Y-m-d'),
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $parent->assignRole($roles['parent']);
        }

        // 6. USER (E-Learning Users) - Shopping & learning
        $regularUsers = [
            [
                'name' => 'Lisa Pratiwi',
                'email' => 'user1@test.com',
                'phone' => '081234567801',
                'gender' => Gender::FEMALE,
            ],
            [
                'name' => 'Rizky Ramadhan',
                'email' => 'user2@test.com',
                'phone' => '081234567802',
                'gender' => Gender::MALE,
            ],
            [
                'name' => 'Dina Mariana',
                'email' => 'user3@test.com',
                'phone' => '081234567803',
                'gender' => Gender::FEMALE,
            ],
        ];

        foreach ($regularUsers as $userData) {
            $user = User::create([
                ...$userData,
                'password' => Hash::make('password'),
                'date_of_birth' => fake()->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->assignRole($roles['user']);
        }

        // 7. Additional random users (for testing)
        User::factory(10)->create()->each(function ($user) use ($roles) {
            // Random role assignment (mostly user, some parents)
            $randomRole = fake()->randomElement([
                $roles['user'],
                $roles['user'],
                $roles['user'],
                $roles['parent'],
            ]);
            $user->assignRole($randomRole);
        });
    }
}
