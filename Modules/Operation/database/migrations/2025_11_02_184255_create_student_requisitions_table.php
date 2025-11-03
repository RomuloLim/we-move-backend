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
        Schema::create('student_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('semester');
            $table->string('protocol')->unique();
            $table->enum('status', ['pending', 'approved', 'reproved', 'expired'])->default('pending');
            $table->string('street_name');
            $table->string('house_number')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('phone_contact');
            $table->date('birth_date');
            $table->string('institution_email');
            $table->string('institution_registration');
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('atuation_form', ['student', 'bolsist', 'teacher', 'prep_course', 'other'])->default('other');
            $table->text('deny_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_requisitions');
    }
};
