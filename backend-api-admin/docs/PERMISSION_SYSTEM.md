# Permission System Documentation

## 📋 Overview

Sistem permission menggunakan **Spatie Laravel Permission** package untuk mengatur akses berdasarkan role dan permission.

## 🔐 Permission Categories

### 1. School Management (SIAKAD)
```php
// School Profile
'view school profile'
'edit school profile'
'view subscription'
'upgrade subscription'

// Students Management
'view students'
'view student details'
'create students'
'update students'
'delete students'

// Teachers Management
'view teachers'
'create teachers'
'update teachers'
'delete teachers'

// Classes Management
'view classes'
'create classes'
'update classes'
'delete classes'

// Parent Profiles
'view parents'
'create parents'
'update parents'
'delete parents'

// Attendance
'view attendance'
'input attendance'
'edit attendance'
'delete attendance'

// Assessment
'view assessments'
'input assessments'
'edit assessments'
'delete assessments'

// Rapor
'view rapor'
'generate rapor pdf' // ⭐ Pro Plan only

// Finance (⭐ Pro Plan only)
'view finance'
'input spp'
'input tabungan'
'edit finance'
'delete finance'

// Analytics
'view school analytics'
```

### 2. Parent Permissions
```php
'view own children'
'view children attendance'
'view children assessments'
'download children rapor'  // ⭐ Pro Plan only
'view children spp'         // ⭐ Pro Plan only
'view children tabungan'    // ⭐ Pro Plan only
```

### 3. Content Management (Public Platform)
```php
// Mentors
'view mentors', 'create mentors', 'update mentors', 'delete mentors'

// Categories
'view categories', 'create categories', 'update categories', 'delete categories'

// Webinars
'view webinars', 'create webinars', 'update webinars', 'delete webinars'

// Courses
'view courses', 'create courses', 'update courses', 'delete courses'

// Products
'view products', 'create products', 'update products', 'delete products'

// Articles
'view articles', 'create articles', 'update articles', 'delete articles'

// Testimonials
'view testimonials', 'create testimonials', 'update testimonials', 'delete testimonials'
```

### 4. E-Commerce & Learning
```php
// Shopping
'add to cart'
'checkout'
'view own orders'

// Learning
'access webinars'
'download products'
'take courses'
'view course progress'
'download certificates'

// Promo Codes
'view promo codes', 'create promo codes', 'update promo codes', 'delete promo codes'
```

### 5. Admin Permissions
```php
// User Management
'view all users', 'create users', 'update users', 'delete users'

// School Management
'view all schools'
'update school subscriptions'

// Orders
'view all orders'
'update order status'

// Manual Operations
'manual enroll course'
'manual generate certificate'

// Site Settings
'view site settings'
'update site settings'

// Analytics
'view sales analytics'
'view platform analytics'

// Roles & Permissions
'view roles', 'create roles', 'update roles', 'delete roles', 'assign permissions'
```

---

## 👥 Role-Permission Matrix

### 1. Admin (Super Admin)
✅ **ALL PERMISSIONS** (Full system access)

### 2. Moderator (Content Manager)
```php
✅ Content Management (Full CRUD):
   - Mentors, Categories, Webinars, Courses, Products, Articles, Testimonials

✅ E-Learning (Bonus):
   - Shopping, Learning, Certificates

❌ SIAKAD (School data)
❌ Site Settings
❌ User Management
```

### 3. Headmaster (Kepala Sekolah)
```php
✅ School Management (Full CRUD):
   - School Profile, Students, Teachers, Classes, Parents
   - Attendance, Assessment, Rapor
   - Finance (⭐ Pro Plan only)
   - Analytics

✅ E-Learning (Bonus):
   - Shopping, Learning, Certificates

❌ Other Schools Data (Multi-tenant isolation)
❌ Platform Settings
```

### 4. Teacher (Guru)
```php
✅ View Students (READ ONLY)
✅ Input: Attendance, Assessment
✅ Generate Rapor PDF (⭐ Pro Plan only)
✅ Input Finance (⭐ Pro Plan only)

✅ E-Learning (Bonus):
   - Shopping, Learning, Certificates

❌ Create/Update/Delete Students
❌ Manage Teachers
❌ Manage Classes
❌ School Settings
```

### 5. Parent (Orang Tua)
```php
✅ View Own Children:
   - Attendance, Assessments
   - Download Rapor (⭐ Pro Plan only)
   - SPP & Tabungan (⭐ Pro Plan only)

✅ E-Learning (Bonus):
   - Shopping, Learning, Certificates

❌ Other Students Data
❌ Input/Edit anything
```

### 6. User (Regular User)
```php
✅ E-Learning Only:
   - Shopping, Cart, Checkout
   - Access Webinars, Download Products
   - Take Courses, Certificates

❌ SIAKAD (School data)
❌ Content Management
```

---

## 🚀 Usage Examples

### 1. In Routes (API/Web)
```php
// api.php atau web.php
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckProPlan;

// Single permission
Route::get('/students', [StudentController::class, 'index'])
    ->middleware('permission:view students');

// Multiple permissions (OR)
Route::post('/students', [StudentController::class, 'store'])
    ->middleware('permission:create students');

// Check Pro Plan + Permission
Route::get('/finance', [FinanceController::class, 'index'])
    ->middleware(['permission:view finance', 'pro.plan']);

// Group with permission
Route::middleware(['auth:sanctum', 'permission:create courses'])->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
});
```

### 2. In Controllers
```php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Method 1: authorize() - throws 403 if fails
        $this->authorize('view students');
        
        return Student::all();
    }

    public function store(Request $request)
    {
        // Method 2: can() - returns boolean
        if (!auth()->user()->can('create students')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return Student::create($request->all());
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        // Method 3: inline check
        abort_unless(auth()->user()->can('delete students'), 403);
        
        $student->delete();
        return response()->noContent();
    }
}
```

### 3. In Blade Templates (Filament)
```php
// Show button only if has permission
@can('create students')
    <button>Add Student</button>
@endcan

// Hide section
@cannot('edit school profile')
    <p>Contact headmaster to edit school profile.</p>
@endcannot

// Check multiple
@canany(['create students', 'update students'])
    <button>Manage Students</button>
@endcanany

// Check role
@role('headmaster')
    <a href="/upgrade">Upgrade to Pro</a>
@endrole
```

### 4. In Filament Resources
```php
namespace App\Filament\Resources;

use Filament\Resources\Resource;

class StudentResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view students');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create students');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update students');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete students');
    }
}
```

### 5. In Models (Policy - Optional)
```php
namespace App\Policies;

use App\Models\User;
use App\Models\Student;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view students');
    }

    public function create(User $user): bool
    {
        return $user->can('create students');
    }

    public function update(User $user, Student $student): bool
    {
        // Check permission + same school
        return $user->can('update students') 
            && $user->hasSchoolRole($student->school_id, 'headmaster');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('delete students')
            && $user->hasSchoolRole($student->school_id, 'headmaster');
    }
}
```

---

## 🎯 Pro Plan Features

Beberapa permission memerlukan **Pro Plan** subscription:

```php
// Check in Controller
public function generateRaporPDF($studentId)
{
    $student = Student::findOrFail($studentId);
    
    // 1. Check permission
    $this->authorize('generate rapor pdf');
    
    // 2. Check Pro Plan
    if (!$student->school->isPro()) {
        return response()->json([
            'error' => 'Pro Plan required',
            'message' => 'Upgrade to Pro Plan to generate PDF rapor'
        ], 403);
    }
    
    // Generate PDF...
}
```

**Pro Plan Only Features:**
- ✅ `generate rapor pdf`
- ✅ `view finance`, `input spp`, `input tabungan`
- ✅ `download children rapor`
- ✅ `view children spp`, `view children tabungan`

---

## 🧪 Testing Permissions

```php
// tests/Feature/PermissionTest.php
use App\Models\User;
use Spatie\Permission\Models\Role;

public function test_teacher_can_view_students()
{
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    
    $this->actingAs($teacher)
        ->get('/api/students')
        ->assertStatus(200);
}

public function test_teacher_cannot_create_students()
{
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    
    $this->actingAs($teacher)
        ->post('/api/students', ['name' => 'Test'])
        ->assertStatus(403);
}

public function test_headmaster_can_create_students()
{
    $headmaster = User::factory()->create();
    $headmaster->assignRole('headmaster');
    
    $this->actingAs($headmaster)
        ->post('/api/students', ['name' => 'Test'])
        ->assertStatus(201);
}
```

---

## 📦 Database Structure

```
permissions
- id
- name (unique)
- guard_name
- created_at, updated_at

roles
- id
- name (unique)
- guard_name
- created_at, updated_at

model_has_permissions (User-Permission pivot)
- permission_id
- model_type (App\Models\User)
- model_id

model_has_roles (User-Role pivot)
- role_id
- model_type (App\Models\User)
- model_id

role_has_permissions (Role-Permission pivot)
- permission_id
- role_id
```

---

## 🔄 Seeding Order

```php
// DatabaseSeeder.php
$this->call([
    PermissionSeeder::class,  // 1. Create all permissions
    RoleSeeder::class,        // 2. Create roles + assign permissions
    UserSeeder::class,        // 3. Create users + assign roles
    // ... rest of seeders
]);
```

---

## 📝 Common Commands

```bash
# Clear permission cache
php artisan permission:cache-reset

# Create permission
php artisan permission:create-permission "delete posts"

# Create role
php artisan permission:create-role moderator

# Show all permissions
php artisan permission:show

# Sync permissions (after adding new ones)
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

---

## 🎓 Summary

### Key Concepts:
1. **Permission** = Specific action ("create students")
2. **Role** = Collection of permissions ("teacher" role)
3. **User** = Has roles, inherits all role permissions
4. **Gate** = Check permission in code (`can()`, `authorize()`)
5. **Policy** = Advanced authorization logic per model

### Best Practices:
- ✅ Use permission names yang descriptive ("view students", bukan "students.view")
- ✅ Check permission di Controller, bukan hanya frontend
- ✅ Combine permission check + business logic (Pro Plan, tenant isolation)
- ✅ Cache permissions untuk performance
- ✅ Test permission logic dengan Feature tests
