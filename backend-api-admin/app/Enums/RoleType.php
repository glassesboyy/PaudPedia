<?php

namespace App\Enums;

enum RoleType: string
{
    case HEADMASTER = 'headmaster';
    case OPERATOR = 'operator';
    case TEACHER = 'teacher';
    case PARENT = 'parent';

    /**
     * Get role display name
     */
    public function label(): string
    {
        return match($this) {
            self::HEADMASTER => 'Kepala Sekolah',
            self::OPERATOR => 'Operator Sekolah',
            self::TEACHER => 'Guru',
            self::PARENT => 'Orang Tua',
        };
    }

    /**
     * Get role description
     */
    public function description(): string
    {
        return match($this) {
            self::HEADMASTER => 'Kepala sekolah dengan akses monitoring dan kontrol finansial lembaga',
            self::OPERATOR => 'Operator sekolah dengan akses manajemen administrasi harian',
            self::TEACHER => 'Guru dengan akses input absensi, penilaian, dan keuangan siswa di kelasnya',
            self::PARENT => 'Orang tua dengan akses monitoring anak',
        };
    }

    /**
     * Get permissions for the role
     */
    public function permissions(): array
    {
        return match($this) {
            self::HEADMASTER => [
                'manage_school',
                'manage_operators',
                'view_teachers',
                'view_students',
                'view_classes',
                'view_parents',
                'view_attendance',
                'view_assessments',
                'view_finances',
                'view_reports',
                'generate_reports',
                'upgrade_subscription',
                'transfer_ownership',
            ],
            self::OPERATOR => [
                'manage_school',
                'manage_teachers',
                'manage_students',
                'manage_classes',
                'manage_parents',
                'view_attendance',
                'view_assessments',
                'view_finances',
                'manage_finances',
                'view_reports',
                'generate_reports',
            ],
            self::TEACHER => [
                'view_students',
                'input_attendance',
                'input_assessments',
                'view_class_data',
                'input_finances',
                'generate_reports',
            ],
            self::PARENT => [
                'view_children',
                'view_child_attendance',
                'view_child_assessments',
                'view_child_finances',
                'download_reports',
            ],
        };
    }

    /**
     * Check if role can access school management (settings, profile)
     */
    public function canManageSchool(): bool
    {
        return in_array($this, [self::HEADMASTER, self::OPERATOR]);
    }

    /**
     * Check if role can manage operators
     */
    public function canManageOperators(): bool
    {
        return $this === self::HEADMASTER;
    }

    /**
     * Check if role can manage teachers
     */
    public function canManageTeachers(): bool
    {
        return $this === self::OPERATOR;
    }

    /**
     * Check if role can input data (attendance, assessments, etc.)
     */
    public function canInputData(): bool
    {
        return in_array($this, [self::HEADMASTER, self::OPERATOR, self::TEACHER]);
    }

    /**
     * Check if role can manage administrative data (CRUD students, teachers, classes, parents)
     */
    public function canManageAdministration(): bool
    {
        return $this === self::OPERATOR;
    }

    /**
     * Check if role has exclusive financial control (subscription & billing)
     */
    public function canManageSubscription(): bool
    {
        return $this === self::HEADMASTER;
    }

    /**
     * Get all role names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all role values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get roles that can be assigned by headmaster
     */
    public static function assignableByHeadmaster(): array
    {
        return [self::OPERATOR];
    }

    /**
     * Get roles that can be assigned by operator
     */
    public static function assignableByOperator(): array
    {
        return [self::TEACHER];
    }
}
