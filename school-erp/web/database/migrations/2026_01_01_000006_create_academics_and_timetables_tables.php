<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->enum('type', ['theory', 'practical', 'both'])->default('theory');
            $table->string('department', 100)->nullable();
            $table->timestamps();
        });

        // 2. Class Subjects Mapping
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete(); // Assigned Subject Teacher
            $table->timestamps();

            $table->unique(['school_class_id', 'subject_id']);
        });

        // 3. Timetables (Weekly Slots)
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete(); // Teacher
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->unsignedTinyInteger('period_number'); // 1 to 8
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('09:45:00');
            $table->string('room_no', 30)->nullable();
            $table->timestamps();

            // Prevent two subjects in same class section at same period
            $table->unique(['school_class_id', 'section_id', 'day_of_week', 'period_number'], 'class_slot_unique');
            // Teacher clash index
            $table->index(['staff_id', 'day_of_week', 'period_number'], 'teacher_slot_idx');
        });

        // 4. Substitutions (For Absent Teachers)
        Schema::create('substitutions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->foreignId('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->foreignId('original_staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('substitute_staff_id')->constrained('staff')->cascadeOnDelete();
            $table->enum('status', ['suggested', 'assigned', 'completed'])->default('assigned');
            $table->string('remarks')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['date', 'timetable_id']);
        });

        // 5. Examinations
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('exam_type', ['quarterly', 'half_yearly', 'annual', 'mid_term', 'unit_test'])->default('quarterly');
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['scheduled', 'ongoing', 'completed'])->default('scheduled');
            $table->timestamps();
        });

        // 6. Exam Schedules per Class & Subject
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time')->default('09:30:00');
            $table->time('end_time')->default('12:30:00');
            $table->unsignedInteger('max_marks')->default(100);
            $table->unsignedInteger('pass_marks')->default(35);
            $table->timestamps();

            $table->unique(['exam_id', 'school_class_id', 'subject_id'], 'exam_class_subject_unique');
        });

        // 7. Student Marks Entry & Grades
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->string('grade', 5)->default('A1'); // A1, A2, B1, B2, C1, C2, D, E
            $table->decimal('grade_point', 4, 1)->default(10.0);
            $table->boolean('is_absent')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_schedule_id', 'student_id']);
        });

        // 8. Homework Assignments
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('assigned_date');
            $table->date('due_date');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework');
        Schema::dropIfExists('marks');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('substitutions');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
    }
};
