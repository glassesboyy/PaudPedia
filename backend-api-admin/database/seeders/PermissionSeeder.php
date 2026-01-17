<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Permissions berdasarkan USE_CASE.md
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 1. SCHOOL MANAGEMENT PERMISSIONS (SIAKAD)
        // ========================================
        
        // School Profile
        Permission::create(['name' => 'view school profile']);
        Permission::create(['name' => 'edit school profile']);
        Permission::create(['name' => 'view subscription']);
        Permission::create(['name' => 'upgrade subscription']);

        // Students Management
        Permission::create(['name' => 'view students']);
        Permission::create(['name' => 'view student details']);
        Permission::create(['name' => 'create students']);
        Permission::create(['name' => 'update students']);
        Permission::create(['name' => 'delete students']);

        // Teachers Management
        Permission::create(['name' => 'view teachers']);
        Permission::create(['name' => 'create teachers']);
        Permission::create(['name' => 'update teachers']);
        Permission::create(['name' => 'delete teachers']);

        // Classes Management
        Permission::create(['name' => 'view classes']);
        Permission::create(['name' => 'create classes']);
        Permission::create(['name' => 'update classes']);
        Permission::create(['name' => 'delete classes']);

        // Parent Profiles Management
        Permission::create(['name' => 'view parents']);
        Permission::create(['name' => 'create parents']);
        Permission::create(['name' => 'update parents']);
        Permission::create(['name' => 'delete parents']);

        // Attendance
        Permission::create(['name' => 'view attendance']);
        Permission::create(['name' => 'input attendance']);
        Permission::create(['name' => 'edit attendance']);
        Permission::create(['name' => 'delete attendance']);

        // Assessment
        Permission::create(['name' => 'view assessments']);
        Permission::create(['name' => 'input assessments']);
        Permission::create(['name' => 'edit assessments']);
        Permission::create(['name' => 'delete assessments']);

        // Rapor
        Permission::create(['name' => 'view rapor']);
        Permission::create(['name' => 'generate rapor pdf']); // Pro Plan only

        // Finance (Pro Plan only)
        Permission::create(['name' => 'view finance']);
        Permission::create(['name' => 'input spp']);
        Permission::create(['name' => 'input tabungan']);
        Permission::create(['name' => 'edit finance']);
        Permission::create(['name' => 'delete finance']);

        // School Analytics
        Permission::create(['name' => 'view school analytics']);

        // ========================================
        // 2. PARENT PERMISSIONS
        // ========================================
        
        Permission::create(['name' => 'view own children']);
        Permission::create(['name' => 'view children attendance']);
        Permission::create(['name' => 'view children assessments']);
        Permission::create(['name' => 'download children rapor']);
        Permission::create(['name' => 'view children spp']);
        Permission::create(['name' => 'view children tabungan']);

        // ========================================
        // 3. CONTENT MANAGEMENT PERMISSIONS (PUBLIC)
        // ========================================
        
        // Mentors
        Permission::create(['name' => 'view mentors']);
        Permission::create(['name' => 'create mentors']);
        Permission::create(['name' => 'update mentors']);
        Permission::create(['name' => 'delete mentors']);

        // Categories
        Permission::create(['name' => 'view categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'update categories']);
        Permission::create(['name' => 'delete categories']);

        // Webinars
        Permission::create(['name' => 'view webinars']);
        Permission::create(['name' => 'create webinars']);
        Permission::create(['name' => 'update webinars']);
        Permission::create(['name' => 'delete webinars']);

        // Courses
        Permission::create(['name' => 'view courses']);
        Permission::create(['name' => 'create courses']);
        Permission::create(['name' => 'update courses']);
        Permission::create(['name' => 'delete courses']);

        // Products
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'delete products']);

        // Articles
        Permission::create(['name' => 'view articles']);
        Permission::create(['name' => 'create articles']);
        Permission::create(['name' => 'update articles']);
        Permission::create(['name' => 'delete articles']);

        // Testimonials
        Permission::create(['name' => 'view testimonials']);
        Permission::create(['name' => 'create testimonials']);
        Permission::create(['name' => 'update testimonials']);
        Permission::create(['name' => 'delete testimonials']);

        // ========================================
        // 4. E-COMMERCE PERMISSIONS
        // ========================================
        
        // Shopping
        Permission::create(['name' => 'add to cart']);
        Permission::create(['name' => 'checkout']);
        Permission::create(['name' => 'view own orders']);

        // Learning
        Permission::create(['name' => 'access webinars']);
        Permission::create(['name' => 'download products']);
        Permission::create(['name' => 'take courses']);
        Permission::create(['name' => 'view course progress']);
        Permission::create(['name' => 'download certificates']);

        // Promo Codes
        Permission::create(['name' => 'view promo codes']);
        Permission::create(['name' => 'create promo codes']);
        Permission::create(['name' => 'update promo codes']);
        Permission::create(['name' => 'delete promo codes']);

        // ========================================
        // 5. ADMIN PERMISSIONS
        // ========================================
        
        // User Management
        Permission::create(['name' => 'view all users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // School Management (Admin)
        Permission::create(['name' => 'view all schools']);
        Permission::create(['name' => 'update school subscriptions']);

        // Orders Management
        Permission::create(['name' => 'view all orders']);
        Permission::create(['name' => 'update order status']);

        // Manual Operations (Troubleshooting)
        Permission::create(['name' => 'manual enroll course']);
        Permission::create(['name' => 'manual generate certificate']);

        // Site Settings
        Permission::create(['name' => 'view site settings']);
        Permission::create(['name' => 'update site settings']);

        // Analytics
        Permission::create(['name' => 'view sales analytics']);
        Permission::create(['name' => 'view platform analytics']);

        // Roles & Permissions Management
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);
        Permission::create(['name' => 'assign permissions']);
    }
}
