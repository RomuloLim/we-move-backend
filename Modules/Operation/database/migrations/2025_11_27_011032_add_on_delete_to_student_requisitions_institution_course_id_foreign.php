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
            $table->dropForeign(['institution_course_id']);
        });

        Schema::table('student_requisitions', function (Blueprint $table) {
            $table->foreign('institution_course_id')->references('id')->on('institution_courses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_requisitions', function (Blueprint $table) {
            $table->dropForeign(['institution_course_id']);
        });

        Schema::table('student_requisitions', function (Blueprint $table) {
            $table->foreign('institution_course_id')->references('id')->on('institution_courses');
        });
    }
};
