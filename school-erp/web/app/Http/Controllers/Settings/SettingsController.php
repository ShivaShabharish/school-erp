<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\FeeReceipt;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StoreItem;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $currentTab = $request->get('tab', 'profile');

        $settings = Setting::pluck('value', 'key')->toArray();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $currentYear = AcademicYear::current();

        $stats = [
            'total_students' => Student::count(),
            'total_staff' => Staff::count(),
            'total_users' => User::count(),
            'total_audit_logs' => AuditLog::count(),
            'app_integration_enabled' => ($settings['app_integration_enabled'] ?? 'false') === 'true',
        ];

        $rolesMatrix = [
            'chairman' => [
                'name' => 'Chairman & Management',
                'description' => 'Full administrative sovereignty across academic, personnel, and institutional finance divisions.',
                'permissions' => ['Full System Access', 'Finance & Ledgers', 'Payroll Approvals', 'Admissions & Promotions', 'Settings & Safety Config'],
                'color' => 'emerald',
            ],
            'principal' => [
                'name' => 'Head of Institution / Principal',
                'description' => 'Executive operational head overseeing daily attendance, faculty rosters, timetables, and academic outcomes.',
                'permissions' => ['Admin Dashboard', 'Academics & CCE Exams', 'Staff Attendance & Substitution', 'Certificates & Bonafide Issuance', 'Circulars & Notice Board'],
                'color' => 'blue',
            ],
            'accountant' => [
                'name' => 'Bursar / School Accountant',
                'description' => 'Cashier operations, student tuition fee collection, POS store registers, and expenditure books.',
                'permissions' => ['Fee Collection Counter', 'Store Inventory & POS Billing', 'Expense Voucher Registry', 'Cash Day-Book & CSV Exports'],
                'color' => 'amber',
            ],
            'teacher' => [
                'name' => 'Teaching Faculty',
                'description' => 'Classroom management, period timetables, formative & summative CCE marks entry, and roll-call.',
                'permissions' => ['Timetable Schedule', 'Section Roll Call', 'Exam Marks Entry Sheet', 'Homework Assignment Board', 'Personal Leave Applications'],
                'color' => 'purple',
            ],
            'staff' => [
                'name' => 'Non-Teaching / Support Staff',
                'description' => 'Campus maintenance, lab technicians, and administration clerks.',
                'permissions' => ['Biometric Punch View', 'Monthly Payslip Download', 'Personal Leave Requests'],
                'color' => 'slate',
            ],
            'parent' => [
                'name' => 'Parent / Legal Guardian',
                'description' => 'Two-way portal access to review ward progress, fee balance, timetable, and direct teacher notes.',
                'permissions' => ['Ward 360 Dossier', 'Fee Dues & Receipts', 'Report Card Download', 'Parent-Teacher Notes Inbox', 'Mobile App Integration (when enabled)'],
                'color' => 'teal',
            ],
        ];

        return view('settings.index', compact(
            'currentTab',
            'settings',
            'academicYears',
            'currentYear',
            'stats',
            'rolesMatrix'
        ));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'school_board' => ['required', 'string', 'max:100'],
            'school_affiliation_no' => ['required', 'string', 'max:50'],
            'school_location' => ['required', 'string', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'receipt_prefix_fees' => ['required', 'string', 'max:30'],
            'receipt_prefix_store' => ['required', 'string', 'max:30'],
            'biometric_api_secret' => ['required', 'string', 'max:60'],
        ]);

        $oldSettings = Setting::pluck('value', 'key')->toArray();

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'system');
        }

        AuditLog::record('settings_profile_updated', null, $oldSettings, $validated);

        return redirect()->route('admin.settings.index', ['tab' => 'profile'])
            ->with('success', 'School profile and institution configuration updated successfully.');
    }

    public function storeAcademicYear(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:academic_years,name', 'regex:/^\d{4}-\d{4}$/'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $year = AcademicYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => false,
        ]);

        AuditLog::record('academic_year_created', $year, null, $validated);

        return redirect()->route('admin.settings.index', ['tab' => 'academic_years'])
            ->with('success', "Academic Year {$year->name} created successfully.");
    }

    public function switchAcademicYear(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $oldCurrent = AcademicYear::current();

        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });

        AuditLog::record('academic_year_switched', $academicYear, [
            'previous' => $oldCurrent?->name,
        ], [
            'active' => $academicYear->name,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'academic_years'])
            ->with('success', "Active Academic Year switched to {$academicYear->name}.");
    }

    public function toggleMobileApp(Request $request): RedirectResponse
    {
        $current = Setting::get('app_integration_enabled', 'false');
        $newVal = ($current === 'true' || $current === true) ? 'false' : 'true';

        Setting::set('app_integration_enabled', $newVal, 'api');

        AuditLog::record('mobile_app_integration_toggled', null, ['enabled' => $current], ['enabled' => $newVal]);

        $statusText = $newVal === 'true' ? 'enabled' : 'disabled';
        return redirect()->route('admin.settings.index', ['tab' => 'safety_api'])
            ->with('success', "Mobile App API Integration has been {$statusText}.");
    }

    public function exportData(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'students');
        $timestamp = now()->format('Y-m-d_His');
        $filename = "{$type}_export_{$timestamp}.csv";

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            if ($type === 'students') {
                fputcsv($handle, ['ID', 'Adm No', 'First Name', 'Last Name', 'Class', 'Section', 'Gender', 'DOB', 'Blood Group', 'Phone', 'Status']);
                Student::with(['schoolClass', 'section'])->chunk(200, function ($students) use ($handle) {
                    foreach ($students as $s) {
                        fputcsv($handle, [
                            $s->id,
                            $s->admission_number,
                            $s->first_name,
                            $s->last_name,
                            $s->schoolClass?->name,
                            $s->section?->name,
                            $s->gender,
                            $s->date_of_birth,
                            $s->blood_group,
                            $s->parent_phone,
                            $s->status,
                        ]);
                    }
                });
            } elseif ($type === 'staff') {
                fputcsv($handle, ['ID', 'Emp Code', 'Name', 'Email', 'Designation', 'Department', 'Phone', 'Status', 'Basic Salary']);
                Staff::with('user')->chunk(100, function ($staffList) use ($handle) {
                    foreach ($staffList as $st) {
                        fputcsv($handle, [
                            $st->id,
                            $st->employee_code,
                            $st->user?->name,
                            $st->user?->email,
                            $st->designation,
                            $st->department,
                            $st->user?->phone,
                            $st->status,
                            $st->basic_salary,
                        ]);
                    }
                });
            } elseif ($type === 'fee_receipts') {
                fputcsv($handle, ['Receipt No', 'Student Adm No', 'Student Name', 'Date', 'Amount Paid', 'Payment Mode', 'Transaction Ref']);
                FeeReceipt::with('student')->chunk(200, function ($receipts) use ($handle) {
                    foreach ($receipts as $r) {
                        fputcsv($handle, [
                            $r->receipt_no,
                            $r->student?->admission_number,
                            $r->student?->full_name,
                            $r->receipt_date,
                            $r->amount_paid,
                            $r->payment_mode,
                            $r->transaction_reference,
                        ]);
                    }
                });
            } elseif ($type === 'expenses') {
                fputcsv($handle, ['Voucher No', 'Category', 'Expense Date', 'Amount', 'Payment Mode', 'Paid To', 'Description']);
                Expense::chunk(200, function ($expenses) use ($handle) {
                    foreach ($expenses as $e) {
                        fputcsv($handle, [
                            $e->voucher_no,
                            $e->category,
                            $e->expense_date,
                            $e->amount,
                            $e->payment_mode,
                            $e->paid_to,
                            $e->description,
                        ]);
                    }
                });
            } elseif ($type === 'store_items') {
                fputcsv($handle, ['Item Code', 'Item Name', 'Category', 'Cost Price', 'Selling Price', 'Stock Qty', 'Low Stock Threshold']);
                StoreItem::chunk(200, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $item->item_code,
                            $item->name,
                            $item->category,
                            $item->cost_price,
                            $item->selling_price,
                            $item->stock_quantity,
                            $item->low_stock_threshold,
                        ]);
                    }
                });
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadSqlBackup(): StreamedResponse
    {
        $timestamp = now()->format('Y_m_d_His');
        $filename = "school_erp_backup_{$timestamp}.sql";

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "-- ==========================================================\n");
            fwrite($handle, "-- TEST SUBJECT HIGHER SECONDARY SCHOOL ERP - DATABASE BACKUP\n");
            fwrite($handle, "-- Generated: " . now()->toRfc2822String() . "\n");
            fwrite($handle, "-- Server: MySQL 8.0 • Laravel 11/12 Engine\n");
            fwrite($handle, "-- ==========================================================\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            $tables = [
                'settings', 'academic_years', 'school_classes', 'sections', 'users',
                'students', 'staff', 'subjects', 'fee_structures', 'fee_allocations',
                'fee_concessions', 'fee_receipts', 'fee_receipt_items', 'store_items',
                'store_sales', 'store_sale_items', 'expenses', 'calendar_events',
                'announcements', 'ptm_schedules', 'parent_notes', 'certificates',
                'audit_logs',
            ];

            foreach ($tables as $table) {
                if (! DB::getSchemaBuilder()->hasTable($table)) {
                    continue;
                }

                fwrite($handle, "-- Table structure and data for `{$table}`\n");
                $rows = DB::table($table)->get();

                if ($rows->isNotEmpty()) {
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $keys = array_map(fn($k) => "`{$k}`", array_keys($rowArray));
                        $values = array_map(function ($val) {
                            if ($val === null) return 'NULL';
                            return "'" . addslashes((string) $val) . "'";
                        }, array_values($rowArray));

                        $sql = "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($handle, $sql);
                    }
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "-- Backup Complete.\n");

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }
}
