<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fee Structures Master
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('name', 100); // "Tuition Fee", "Term Exam Fee", "Lab Fee", "Library", "Sports", "Development"
            $table->string('term', 20)->default('Term 1'); // Term 1, Term 2, Term 3, Annual
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamps();
            $table->index(['academic_year_id', 'school_class_id', 'term']);
        });

        // 2. Fee Concessions & Scholarships
        Schema::create('fee_concessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('title', 100); // "Sibling Discount", "Merit Scholarship", "Staff Ward", "RTE Quota"
            $table->enum('concession_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 10, 2); // 25.00 (%) or 5000.00 (INR)
            $table->string('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Student Fee Allocations (Dues per term/installment)
        Schema::create('fee_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('concession_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2);
            $table->string('status', 20)->default('pending')->index(); // pending, partial, paid, waived
            $table->date('due_date');
            $table->timestamps();
            $table->index(['student_id', 'status']);
        });

        // 4. Update Fee Receipts Table with extra metadata
        Schema::table('fee_receipts', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('student_id')->constrained('academic_years')->nullOnDelete();
            $table->decimal('concession_applied', 10, 2)->default(0)->after('amount_paid');
            $table->string('transaction_reference', 100)->nullable()->after('payment_method');
        });

        // 5. Fee Receipt Items Breakdown
        Schema::create('fee_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_receipt_id')->constrained('fee_receipts')->cascadeOnDelete();
            $table->foreignId('fee_allocation_id')->nullable()->constrained('fee_allocations')->nullOnDelete();
            $table->string('description', 150);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // 6. Store Sales Header (POS invoices)
        Schema::create('store_sales', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no', 50)->unique(); // SCH/2026-27/STR-00001
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_type', 30)->default('student'); // student, staff, parent, visitor
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('cost_amount', 10, 2)->default(0);
            $table->decimal('profit_amount', 10, 2)->default(0);
            $table->string('payment_method', 30)->default('Cash'); // Cash, UPI, Card
            $table->date('sale_date')->index();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Store Sale Items Detail
        Schema::create('store_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_sale_id')->constrained('store_sales')->cascadeOnDelete();
            $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_sale_items');
        Schema::dropIfExists('store_sales');
        Schema::dropIfExists('fee_receipt_items');

        Schema::table('fee_receipts', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'concession_applied', 'transaction_reference']);
        });

        Schema::dropIfExists('fee_allocations');
        Schema::dropIfExists('fee_concessions');
        Schema::dropIfExists('fee_structures');
    }
};
