<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_requisitions', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['institution_id', 'course_id']);

            $table->foreignId('institution_course_id')->constrained('institution_courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_requisitions', function (Blueprint $table) {
            $table->dropForeign(['institution_course_id']);
            $table->dropColumn('institution_course_id');

            $table->foreignId('institution_id')->constrained('institutions');
            $table->foreignId('course_id')->constrained('courses');
        });
    }
};
