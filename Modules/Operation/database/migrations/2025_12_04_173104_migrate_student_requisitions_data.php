<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration updates student_requisitions.student_id from user_id to actual student.id
     */
    public function up(): void
    {
        // Update student_requisitions.student_id to use students.id instead of users.id
        DB::statement('
            UPDATE student_requisitions sr
            SET student_id = s.id
            FROM students s
            WHERE sr.student_id = s.user_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert student_requisitions.student_id back to user_id
        DB::statement('
            UPDATE student_requisitions sr
            SET student_id = s.user_id
            FROM students s
            WHERE sr.student_id = s.id
        ');
    }
};
