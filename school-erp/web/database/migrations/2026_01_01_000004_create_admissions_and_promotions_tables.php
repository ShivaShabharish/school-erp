<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Admission Enquiries Table
        Schema::create('admission_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_no', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('gender', 10)->default('male');
            $table->date('dob')->nullable();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('parent_name', 150);
            $table->string('phone', 30);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, reviewed, admitted, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Add extended profile fields to Students Table
        Schema::table('students', function (Blueprint $table) {
            $table->text('address')->nullable()->after('emergency_contact');
            $table->string('city', 100)->default('Pudukkottai')->after('address');
            $table->string('state', 100)->default('Tamil Nadu')->after('city');
            $table->string('pincode', 20)->nullable()->after('state');
            $table->string('father_name', 150)->nullable()->after('pincode');
            $table->string('mother_name', 150)->nullable()->after('father_name');
            $table->string('father_occupation', 100)->nullable()->after('mother_name');
            $table->string('mother_occupation', 100)->nullable()->after('father_occupation');
            $table->string('parent_phone', 30)->nullable()->after('mother_occupation');
            $table->string('parent_email', 100)->nullable()->after('parent_phone');
            $table->string('previous_school', 200)->nullable()->after('parent_email');
            $table->string('tc_number', 50)->nullable()->after('previous_school');
            $table->json('documents')->nullable()->after('tc_number');
        });

        // 3. Student Promotions Log Table
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('from_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('from_section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('to_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('to_section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('promoted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('promoted')->index(); // promoted, retained, graduated
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'state',
                'pincode',
                'father_name',
                'mother_name',
                'father_occupation',
                'mother_occupation',
                'parent_phone',
                'parent_email',
                'previous_school',
                'tc_number',
                'documents',
            ]);
        });

        Schema::dropIfExists('admission_enquiries');
    }
};
