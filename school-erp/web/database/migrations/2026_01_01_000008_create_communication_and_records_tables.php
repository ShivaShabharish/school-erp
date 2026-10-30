<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Calendar Events & Holidays
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('event_type', 30)->default('function'); // holiday, exam, function, ptm, sports, academic
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_holiday')->default(false)->index();
            $table->string('target_audience', 30)->default('all'); // all, students, staff, parents
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['academic_year_id', 'start_date']);
        });

        // 2. Targeted Announcements & Circulars
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('content');
            $table->string('category', 30)->default('general'); // general, academic, urgent, event, sports
            $table->string('target_type', 30)->default('all'); // all, class, staff, parents
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->date('publish_date')->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Parent-Teacher Meeting (PTM) Schedules
        Schema::create('ptm_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue', 100)->default('Classroom');
            $table->text('agenda')->nullable();
            $table->string('status', 30)->default('scheduled'); // scheduled, completed, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Parent Note Communication Inbox
        Schema::create('parent_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('subject', 200);
            $table->text('message');
            $table->text('reply')->nullable();
            $table->dateTime('replied_at')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending, replied, closed
            $table->timestamps();
        });

        // 5. Official Certificates (TC & Bonafide)
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_no', 50)->unique(); // SCH/2026-27/BON-00001 or SCH/2026-27/TC-00001
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('certificate_type', 30); // bonafide, transfer_certificate, conduct
            $table->date('issue_date')->index();
            $table->string('reason', 200)->nullable();
            $table->json('details')->nullable(); // extra fields for TC (leaving date, conduct, remarks)
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('parent_notes');
        Schema::dropIfExists('ptm_schedules');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('calendar_events');
    }
};
