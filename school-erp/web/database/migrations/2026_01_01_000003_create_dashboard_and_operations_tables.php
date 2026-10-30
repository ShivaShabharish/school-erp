<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Staff Table
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_code', 30)->unique();
            $table->string('designation', 100);
            $table->string('department', 100);
            $table->string('qualification', 100)->nullable();
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->string('status', 30)->default('active')->index(); // active, on_leave, inactive
            $table->timestamps();
        });

        // 2. Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('gender', 10)->default('male');
            $table->date('dob')->nullable();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('roll_number', 20)->nullable();
            $table->date('admission_date')->nullable();
            $table->string('status', 30)->default('active')->index(); // active, alumni, transferred
            $table->string('blood_group', 10)->nullable();
            $table->string('emergency_contact', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Student Attendance
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('status', 20)->default('present')->index(); // present, absent, late, half_day
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'date']);
        });

        // 4. Staff Attendance & Biometric Punches
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date')->index();
            $table->time('punch_in_time')->nullable();
            $table->time('punch_out_time')->nullable();
            $table->string('status', 20)->default('present')->index(); // present, absent, late, on_leave
            $table->string('device_id', 50)->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'date']);
        });

        // 5. Fee Receipts
        Schema::create('fee_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 50)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method', 30)->default('Cash'); // Cash, UPI, Cheque, Bank Transfer
            $table->date('receipt_date')->index();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Expenses Ledger
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->index(); // Maintenance, Utilities, Stationery, Salaries, Events
            $table->string('title');
            $table->string('vendor_name')->nullable();
            $table->string('bill_no', 50)->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->index();
            $table->string('payment_method', 30)->default('Bank Transfer');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Store Items
        Schema::create('store_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 50)->index(); // Uniform, Book, Notebook, Bag, Stationery
            $table->string('item_code', 50)->unique();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->integer('stock_quantity')->default(0)->index();
            $table->integer('alert_threshold')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_items');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('fee_receipts');
        Schema::dropIfExists('staff_attendances');
        Schema::dropIfExists('student_attendances');
        Schema::dropIfExists('students');
        Schema::dropIfExists('staff');
    }
};
