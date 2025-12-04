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
            // Drop the existing foreign key constraint
            $table->dropForeign(['student_id']);

            // Add the corrected foreign key pointing to students table
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_requisitions', function (Blueprint $table) {
            // Drop the corrected foreign key
            $table->dropForeign(['student_id']);

            // Restore the old foreign key pointing to users table
            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
