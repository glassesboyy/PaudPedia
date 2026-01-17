<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Assign permissions to roles berdasarkan USE_CASE.md
     */
    public function run(): void
    {
        // ========================================
        // 1. ADMIN ROLE (Super Admin)
        // ========================================
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all()); // Full access

        // ========================================
        // 2. MODERATOR ROLE (Content Manager)
        // ========================================
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $moderator->givePermissionTo([
            // Content Management (Full CRUD)
            'view mentors', 'create mentors', 'update mentors', 'delete mentors',
            'view categories', 'create categories', 'update categories', 'delete categories',
            'view webinars', 'create webinars', 'update webinars', 'delete webinars',
            'view courses', 'create courses', 'update courses', 'delete courses',
            'view products', 'create products', 'update products', 'delete products',
            'view articles', 'create articles', 'update articles', 'delete articles',
            'view testimonials', 'create testimonials', 'update testimonials', 'delete testimonials',
            
            // Can also shop & learn (bonus)
            'add to cart', 'checkout', 'view own orders',
            'access webinars', 'download products', 'take courses', 
            'view course progress', 'download certificates',
        ]);

        // ========================================
        // 3. HEADMASTER ROLE (Kepala Sekolah)
        // ========================================
        $headmaster = Role::firstOrCreate(['name' => 'headmaster']);
        $headmaster->givePermissionTo([
            // School Profile
            'view school profile', 'edit school profile',
            'view subscription', 'upgrade subscription',
            
            // Students Management (Full CRUD)
            'view students', 'view student details',
            'create students', 'update students', 'delete students',
            
            // Teachers Management (Full CRUD)
            'view teachers', 'create teachers', 'update teachers', 'delete teachers',
            
            // Classes Management (Full CRUD)
            'view classes', 'create classes', 'update classes', 'delete classes',
            
            // Parent Profiles Management (Full CRUD)
            'view parents', 'create parents', 'update parents', 'delete parents',
            
            // Attendance (Full CRUD)
            'view attendance', 'input attendance', 'edit attendance', 'delete attendance',
            
            // Assessment (Full CRUD)
            'view assessments', 'input assessments', 'edit assessments', 'delete assessments',
            
            // Rapor
            'view rapor', 'generate rapor pdf', // PDF depends on Pro Plan
            
            // Finance (Full CRUD - Pro Plan only)
            'view finance', 'input spp', 'input tabungan', 'edit finance', 'delete finance',
            
            // Analytics
            'view school analytics',
            
            // Can also shop & learn (bonus)
            'add to cart', 'checkout', 'view own orders',
            'access webinars', 'download products', 'take courses', 
            'view course progress', 'download certificates',
        ]);

        // ========================================
        // 4. TEACHER ROLE (Guru)
        // ========================================
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo([
            // Students (READ ONLY)
            'view students', 'view student details',
            
            // Attendance (Input only)
            'view attendance', 'input attendance',
            
            // Assessment (Input only)
            'view assessments', 'input assessments',
            
            // Rapor (Read + Generate)
            'view rapor', 'generate rapor pdf', // PDF depends on Pro Plan
            
            // Finance (Input only - Pro Plan only)
            'view finance', 'input spp', 'input tabungan',
            
            // Can also shop & learn (bonus)
            'add to cart', 'checkout', 'view own orders',
            'access webinars', 'download products', 'take courses', 
            'view course progress', 'download certificates',
        ]);

        // ========================================
        // 5. PARENT ROLE (Orang Tua)
        // ========================================
        $parent = Role::firstOrCreate(['name' => 'parent']);
        $parent->givePermissionTo([
            // Children Monitoring (READ ONLY)
            'view own children',
            'view children attendance',
            'view children assessments',
            'download children rapor', // Depends on Pro Plan
            'view children spp',        // Depends on Pro Plan
            'view children tabungan',   // Depends on Pro Plan
            
            // Can also shop & learn (bonus)
            'add to cart', 'checkout', 'view own orders',
            'access webinars', 'download products', 'take courses', 
            'view course progress', 'download certificates',
        ]);

        // ========================================
        // 6. USER ROLE (Regular User - E-Learning)
        // ========================================
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            // Shopping & Learning
            'add to cart',
            'checkout',
            'view own orders',
            'access webinars',
            'download products',
            'take courses',
            'view course progress',
            'download certificates',
        ]);
    }
}
