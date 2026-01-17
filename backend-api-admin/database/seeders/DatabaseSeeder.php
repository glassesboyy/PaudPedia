<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in correct order (respect foreign keys)
        $this->call([
            // Permissions & Roles (MUST BE FIRST)
            PermissionSeeder::class,
            RoleSeeder::class,
            
            // Core data
            UserSeeder::class,
            CategorySeeder::class,
            MentorSeeder::class,
            
            // School management
            SchoolSeeder::class,
            SchoolMemberSeeder::class,
            TeacherSeeder::class,
            ClassRoomSeeder::class,
            ParentProfileSeeder::class,
            StudentSeeder::class,
            
            // Student data (requires students)
            AttendanceSeeder::class,
            AssessmentSeeder::class,
            FinanceSeeder::class,
            
            // Content management
            WebinarSeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            LessonSeeder::class,
            ProductSeeder::class,
            ArticleSeeder::class,
            TestimonialSeeder::class,
            
            // Commerce
            PromoCodeSeeder::class,
            OrderSeeder::class,
            CourseEnrollmentSeeder::class,
            
            // Site settings
            SiteSettingSeeder::class,
        ]);
    }
}
