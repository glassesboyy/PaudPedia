<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add 'operator' to the role_type ENUM column in school_members table.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE school_members MODIFY COLUMN role_type ENUM('headmaster', 'operator', 'teacher', 'parent') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE school_members MODIFY COLUMN role_type ENUM('headmaster', 'teacher', 'parent') NOT NULL");
    }
};
