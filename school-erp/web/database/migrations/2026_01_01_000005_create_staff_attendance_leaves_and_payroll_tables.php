<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add additional profile fields to staff if not already there
        Schema::table('staff', function (Blueprint $table) {
            $table->string('gender', 20)->default('female')->after('qualification');
            $table->date('dob')->nullable()->after('gender');
            $table->string('blood_group', 10)->nullable()->after('dob');
            $table->string('bank_account_no', 50)->nullable()->after('basic_salary');
            $table->string('bank_name', 100)->nullable()->after('bank_account_no');
            $table->string('bank_ifsc', 30)->nullable()->after('bank_name');
            $table->text('address')->nullable()->after('bank_ifsc');
        });

        // Leave Types (CL, ML, EL)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->unsignedInteger('days_per_year')->default(12);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Staff Leave Quotas per Academic Year
        Schema::create('staff_leave_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->decimal('allocated_days', 5, 1)->default(12.0);
            $table->decimal('used_days', 5, 1)->default(0.0);
            $table->timestamps();

            $table->unique(['staff_id', 'leave_type_id', 'academic_year_id'], 'staff_leave_quota_unique');
        });

        // Leave Applications
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 4, 1)->default(1.0);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Monthly Payrolls & Payslips
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('month', 7)->index(); // YYYY-MM (e.g. 2026-10)
            $table->string('payslip_number', 50)->unique();
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('da_amount', 10, 2)->default(0.00); // Dearness Allowance (20%)
            $table->decimal('hra_amount', 10, 2)->default(0.00); // House Rent Allowance (10%)
            $table->decimal('special_allowance', 10, 2)->default(0.00);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('pf_deduction', 10, 2)->default(0.00); // PF (12%)
            $table->decimal('tax_deduction', 10, 2)->default(0.00); // Professional Tax / TDS
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2);
            $table->unsignedInteger('working_days')->default(26);
            $table->unsignedInteger('present_days')->default(25);
            $table->unsignedInteger('leave_days')->default(1);
            $table->enum('status', ['pending', 'paid'])->default('paid')->index();
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 50)->default('Bank Transfer / NEFT');
            $table->timestamps();

            $table->unique(['staff_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('staff_leave_quotas');
        Schema::dropIfExists('leave_types');

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['gender', 'dob', 'blood_group', 'bank_account_no', 'bank_name', 'bank_ifsc', 'address']);
        });
    }
};
