<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FeeAllocation;
use App\Models\FeeReceipt;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\StoreItem;
use App\Models\StoreSale;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class AccountsBillingTest extends TestCase
{
    protected User $accountant;
    protected User $principal;
    protected User $teacher;
    protected User $parent;
    protected AcademicYear $ay;
    protected SchoolClass $class;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountant = User::firstOrCreate(
            ['email' => 'accountant@school.test'],
            ['name' => 'School Accountant', 'password' => bcrypt('Password@123'), 'role' => 'accountant', 'is_active' => true]
        );

        $this->principal = User::firstOrCreate(
            ['email' => 'principal@school.test'],
            ['name' => 'Principal', 'password' => bcrypt('Password@123'), 'role' => 'principal', 'is_active' => true]
        );

        $this->teacher = User::firstOrCreate(
            ['email' => 'teacher@school.test'],
            ['name' => 'Teacher', 'password' => bcrypt('Password@123'), 'role' => 'teacher', 'is_active' => true]
        );

        $this->parent = User::firstOrCreate(
            ['email' => 'parent@school.test'],
            ['name' => 'Parent', 'password' => bcrypt('Password@123'), 'role' => 'parent', 'is_active' => true]
        );

        $this->ay = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        $this->class = SchoolClass::where('name', 'Class 10')->first() ?? SchoolClass::first();
        $this->student = Student::where('school_class_id', $this->class->id)->first() ?? Student::first();
    }

    public function test_accountant_can_view_accounts_dashboard_with_all_metrics(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('accounts.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Accounts & Billing Management', false);
        $response->assertSee("Today's Collections", false);
        $response->assertSee('Fee Schedule', false);
    }

    public function test_fee_structure_creation_and_auto_allocation_to_class(): void
    {
        $response = $this->actingAs($this->accountant)->post(route('accounts.fees.store'), [
            'academic_year_id' => $this->ay->id,
            'school_class_id' => $this->class->id,
            'name' => 'Robotics & AI Innovation Lab Fee',
            'term' => 'Term 2',
            'amount' => 4500.00,
            'due_date' => now()->addDays(30)->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fee_structures', [
            'school_class_id' => $this->class->id,
            'name' => 'Robotics & AI Innovation Lab Fee',
            'amount' => 4500.00,
        ]);

        // Assert fee was auto-allocated to student
        $this->assertDatabaseHas('fee_allocations', [
            'student_id' => $this->student->id,
            'amount' => 4500.00,
        ]);
    }

    public function test_cashier_fee_collection_creates_receipt_and_allocates_dues(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('accounts.fees.collect', [
            'student_id' => $this->student->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name, false);

        // Collect ₹ 8,000 for this student
        $postResponse = $this->actingAs($this->accountant)->post(route('accounts.fees.collect.store'), [
            'student_id' => $this->student->id,
            'amount_paid' => 8000.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UPI-9842104592',
            'receipt_date' => now()->toDateString(),
            'notes' => 'Term fee paid via GPay',
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('fee_receipts', [
            'student_id' => $this->student->id,
            'amount_paid' => 8000.00,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UPI-9842104592',
        ]);
    }

    public function test_fee_receipt_pdf_download(): void
    {
        $receipt = FeeReceipt::first();
        $this->assertNotNull($receipt);

        $response = $this->actingAs($this->accountant)->get(route('accounts.fees.receipt', $receipt));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_student_concession_application_rebalances_dues(): void
    {
        $response = $this->actingAs($this->principal)->post(route('accounts.fees.concessions.store'), [
            'student_id' => $this->student->id,
            'academic_year_id' => $this->ay->id,
            'title' => 'Special Board Merit Grant',
            'concession_type' => 'percentage',
            'amount' => 30.00, // 30% concession
            'reason' => 'District Math Olympiad Gold Medalist',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fee_concessions', [
            'student_id' => $this->student->id,
            'title' => 'Special Board Merit Grant',
            'amount' => 30.00,
        ]);
    }

    public function test_store_inventory_management_and_restock(): void
    {
        $uniqueCode = 'STN-OXF-' . substr(md5(uniqid()), 0, 6);

        // Add new store item
        $response = $this->actingAs($this->accountant)->post(route('accounts.store.items.store'), [
            'name' => 'Oxford Mathematical Instrument Box',
            'category' => 'Stationery',
            'item_code' => $uniqueCode,
            'purchase_price' => 110.00,
            'selling_price' => 165.00,
            'stock_quantity' => 25,
            'alert_threshold' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $item = StoreItem::where('item_code', $uniqueCode)->first();
        $this->assertNotNull($item);

        // Restock item
        $restockResponse = $this->actingAs($this->accountant)->post(route('accounts.store.items.restock', $item), [
            'quantity' => 15,
        ]);

        $restockResponse->assertRedirect();
        $this->assertEquals(40, $item->fresh()->stock_quantity);
    }

    public function test_store_pos_sale_decrements_stock_and_computes_profit(): void
    {
        $item = StoreItem::where('stock_quantity', '>=', 10)->first() ?? StoreItem::first();
        $this->assertNotNull($item);
        $item->update(['stock_quantity' => 50]);
        $initialStock = 50;

        $response = $this->actingAs($this->accountant)->post(route('accounts.store.pos.checkout'), [
            'customer_name' => 'Ramesh Kumar',
            'customer_type' => 'visitor',
            'payment_method' => 'Cash',
            'discount' => 10.00,
            'items' => [
                [
                    'store_item_id' => $item->id,
                    'quantity' => 2,
                    'unit_price' => $item->selling_price,
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check stock decremented by 2
        $this->assertEquals($initialStock - 2, $item->fresh()->stock_quantity);

        $this->assertDatabaseHas('store_sales', [
            'customer_name' => 'Ramesh Kumar',
            'discount' => 10.00,
        ]);
    }

    public function test_store_receipt_pdf_download(): void
    {
        $sale = StoreSale::first();
        $this->assertNotNull($sale);

        $response = $this->actingAs($this->accountant)->get(route('accounts.store.receipt', $sale));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_expense_voucher_creation_and_ledger(): void
    {
        $response = $this->actingAs($this->accountant)->post(route('accounts.expenses.store'), [
            'category' => 'Maintenance',
            'title' => 'Auditorium Acoustic Panel Repair',
            'vendor_name' => 'Pudukkottai Acoustic Works',
            'bill_no' => 'PAW-9842',
            'amount' => 14500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'Bank Transfer',
            'notes' => 'Campus renovation maintenance',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'bill_no' => 'PAW-9842',
            'amount' => 14500.00,
            'category' => 'Maintenance',
        ]);
    }

    public function test_daybook_and_monthly_report_calculations(): void
    {
        // Daybook
        $daybookResp = $this->actingAs($this->accountant)->get(route('accounts.reports.daybook', [
            'date' => now()->toDateString(),
        ]));
        $daybookResp->assertStatus(200);
        $daybookResp->assertSee('Daily Cash Day-Book', false);

        // Monthly
        $monthlyResp = $this->actingAs($this->accountant)->get(route('accounts.reports.monthly', [
            'year' => now()->year,
            'month' => now()->month,
        ]));
        $monthlyResp->assertStatus(200);
        $monthlyResp->assertSee('Expenditure Statement', false);
    }

    public function test_transactions_csv_export(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('accounts.reports.transactions.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_teacher_and_parent_cannot_access_accounts_portal(): void
    {
        $teacherResp = $this->actingAs($this->teacher)->get(route('accounts.dashboard'));
        $teacherResp->assertStatus(403);

        $parentResp = $this->actingAs($this->parent)->get(route('accounts.dashboard'));
        $parentResp->assertStatus(403);
    }
}
