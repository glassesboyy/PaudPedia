<?php

namespace App\Enums;

enum RoleType: string
{
    case HEADMASTER = 'headmaster';
    case TEACHER = 'teacher';
    case PARENT = 'parent';

    /**
     * Get role display name
     */
    public function label(): string
    {
        return match($this) {
            self::HEADMASTER => 'Kepala Sekolah',
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
            self::HEADMASTER => 'Kepala sekolah dengan akses penuh ke manajemen sekolah',
            self::TEACHER => 'Guru dengan akses input absensi dan penilaian',
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
                'manage_teachers',
                'manage_students',
                'manage_classes',
                'manage_parents',
                'view_attendance',
                'view_assessments',
                'view_finances',
                'manage_finances',
                'generate_reports',
                'upgrade_subscription',
            ],
            self::TEACHER => [
                'view_students',
                'input_attendance',
                'input_assessments',
                'view_class_data',
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
     * Check if role can access school management
     */
    public function canManageSchool(): bool
    {
        return $this === self::HEADMASTER;
    }

    /**
     * Check if role can input data
     */
    public function canInputData(): bool
    {
        return in_array($this, [self::HEADMASTER, self::TEACHER]);
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
}
