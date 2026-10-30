<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\CalendarEvent;
use App\Models\Certificate;
use App\Models\Expense;
use App\Models\FeeReceipt;
use App\Models\ParentNote;
use App\Models\PtmSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StoreItem;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();

        // 1. Academic Years
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31',
                'is_current' => true,
            ]
        );

        AcademicYear::firstOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_current' => false,
            ]
        );

        // 2. Classes (LKG to 12) & Sections (A, B)
        $classes = [
            'LKG' => 1,
            'UKG' => 2,
            'Class 1' => 3,
            'Class 2' => 4,
            'Class 3' => 5,
            'Class 4' => 6,
            'Class 5' => 7,
            'Class 6' => 8,
            'Class 7' => 9,
            'Class 8' => 10,
            'Class 9' => 11,
            'Class 10' => 12,
            'Class 11' => 13,
            'Class 12' => 14,
        ];

        $allSections = [];
        foreach ($classes as $className => $seq) {
            $class = SchoolClass::firstOrCreate(
                ['name' => $className],
                ['order_sequence' => $seq]
            );

            $secA = Section::firstOrCreate(
                ['school_class_id' => $class->id, 'name' => 'A'],
                ['room_no' => 'Room ' . (100 + $seq), 'max_capacity' => 40]
            );
            $secB = Section::firstOrCreate(
                ['school_class_id' => $class->id, 'name' => 'B'],
                ['room_no' => 'Room ' . (200 + $seq), 'max_capacity' => 40]
            );

            $allSections[] = $secA;
            $allSections[] = $secB;
        }

        // 3. Demo Accounts with Password@123
        $demoUsers = [
            [
                'name' => 'Dr. R. Shanmugasundaram',
                'email' => 'chairman@school.test',
                'role' => 'chairman',
                'phone' => '+91 98421 00001',
            ],
            [
                'name' => 'Prof. S. Meenakshi',
                'email' => 'principal@school.test',
                'role' => 'principal',
                'phone' => '+91 98421 00002',
            ],
            [
                'name' => 'K. Ramanathan',
                'email' => 'accountant@school.test',
                'role' => 'accountant',
                'phone' => '+91 98421 00003',
            ],
            [
                'name' => 'M. Kavitha (Mathematics)',
                'email' => 'teacher@school.test',
                'role' => 'teacher',
                'phone' => '+91 98421 00004',
            ],
            [
                'name' => 'P. Santhosh Kumar (Parent)',
                'email' => 'parent@school.test',
                'role' => 'parent',
                'phone' => '+91 98421 00005',
            ],
            [
                'name' => 'G. Balaji (Office Staff)',
                'email' => 'staff@school.test',
                'role' => 'staff',
                'phone' => '+91 98421 00006',
            ],
        ];

        $usersMap = [];
        foreach ($demoUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Password@123'),
                    'role' => $userData['role'],
                    'phone' => $userData['phone'],
                    'is_active' => true,
                ]
            );
            $usersMap[$userData['role']] = $user;
        }

        // 4. Staff Profiles (~25 staff)
        $staffProfiles = [
            ['user_id' => $usersMap['principal']->id, 'code' => 'STF-001', 'desig' => 'Principal', 'dept' => 'Administration', 'salary' => 85000],
            ['user_id' => $usersMap['accountant']->id, 'code' => 'STF-002', 'desig' => 'Chief Accountant', 'dept' => 'Accounts & Finance', 'salary' => 45000],
            ['user_id' => $usersMap['teacher']->id, 'code' => 'STF-003', 'desig' => 'Senior PGT Mathematics', 'dept' => 'Mathematics', 'salary' => 52000],
            ['user_id' => $usersMap['staff']->id, 'code' => 'STF-004', 'desig' => 'Office Superintendent', 'dept' => 'Administration', 'salary' => 32000],
        ];

        $tamilStaffNames = [
            ['T. Subramanian', 'PGT Physics', 'Science'],
            ['S. Lakshmi', 'PGT Chemistry', 'Science'],
            ['R. Manoharan', 'PGT Biology', 'Science'],
            ['V. Kalyani', 'TGT Tamil', 'Tamil Language'],
            ['D. Anand', 'TGT English', 'English Language'],
            ['P. Revathi', 'TGT Social Science', 'Social Studies'],
            ['K. Murugan', 'Computer Science Instructor', 'Computer Science'],
            ['S. Vijayalakshmi', 'Physical Education Director', 'Sports'],
            ['M. Senthil', 'Junior Assistant', 'Accounts & Finance'],
            ['B. Saravanan', 'Librarian', 'Library'],
            ['N. Uma', 'Primary Teacher', 'Primary Section'],
            ['K. Deepa', 'Primary Teacher', 'Primary Section'],
            ['R. Priya', 'Kindergarten Coordinator', 'Kindergarten'],
            ['A. Vijay', 'Art & Craft Teacher', 'Fine Arts'],
            ['C. Thangavel', 'Lab Assistant', 'Science'],
            ['P. Kannan', 'Transport Coordinator', 'Transport'],
            ['M. Ganesan', 'Storekeeper', 'School Store'],
            ['S. Rajesh', 'Music Instructor', 'Fine Arts'],
            ['E. Jayanthi', 'Special Educator', 'Academics'],
            ['J. Robert', 'French Teacher', 'Languages'],
            ['T. Geetha', 'Junior Accountant', 'Accounts & Finance'],
        ];

        $createdStaff = [];
        foreach ($staffProfiles as $sp) {
            $staff = Staff::firstOrCreate(
                ['employee_code' => $sp['code']],
                [
                    'user_id' => $sp['user_id'],
                    'designation' => $sp['desig'],
                    'department' => $sp['dept'],
                    'qualification' => 'M.Sc., B.Ed.',
                    'joining_date' => '2020-06-01',
                    'basic_salary' => $sp['salary'],
                    'status' => 'active',
                ]
            );
            $createdStaff[] = $staff;
        }

        foreach ($tamilStaffNames as $idx => $tsn) {
            $code = 'STF-' . str_pad($idx + 5, 3, '0', STR_PAD_LEFT);
            $user = User::firstOrCreate(
                ['email' => "staff{$idx}@school.test"],
                [
                    'name' => $tsn[0],
                    'password' => Hash::make('Password@123'),
                    'role' => 'teacher',
                    'phone' => '+91 98421 ' . str_pad($idx + 10, 5, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]
            );

            $staff = Staff::firstOrCreate(
                ['employee_code' => $code],
                [
                    'user_id' => $user->id,
                    'designation' => $tsn[1],
                    'department' => $tsn[2],
                    'qualification' => 'M.A., B.Ed.',
                    'joining_date' => '2021-06-15',
                    'basic_salary' => 38000,
                    'status' => 'active',
                ]
            );
            $createdStaff[] = $staff;
        }

        // 5. Staff Attendance Records Today
        foreach ($createdStaff as $idx => $stf) {
            $punchTime = match ($idx) {
                0 => '08:25:00', // On time
                1 => '08:52:00', // Late
                2 => '08:15:00', // On time
                3 => '09:05:00', // Late
                4 => null,       // On leave
                default => '08:' . str_pad(rand(10, 40), 2, '0', STR_PAD_LEFT) . ':00',
            };

            $status = match ($idx) {
                1, 3 => 'late',
                4 => 'on_leave',
                default => 'present',
            };

            StaffAttendance::firstOrCreate(
                ['staff_id' => $stf->id, 'date' => $today],
                [
                    'punch_in_time' => $punchTime,
                    'punch_out_time' => $status !== 'on_leave' ? '16:30:00' : null,
                    'status' => $status,
                    'device_id' => 'BIO-GATE-01',
                ]
            );
        }

        // 5b. Leave Types, Quotas & Applications
        $leaveTypes = [
            ['name' => 'Casual Leave', 'code' => 'CL', 'days_per_year' => 12, 'description' => 'Discretionary casual leave'],
            ['name' => 'Medical Leave', 'code' => 'ML', 'days_per_year' => 10, 'description' => 'Certified sick and medical absence'],
            ['name' => 'Earned Leave', 'code' => 'EL', 'days_per_year' => 15, 'description' => 'Earned privilege vacation leave'],
        ];

        $createdLeaveTypes = [];
        foreach ($leaveTypes as $lt) {
            $createdLeaveTypes[] = \App\Models\LeaveType::firstOrCreate(['code' => $lt['code']], $lt);
        }

        foreach ($createdStaff as $stf) {
            foreach ($createdLeaveTypes as $clt) {
                \App\Models\StaffLeaveQuota::firstOrCreate(
                    [
                        'staff_id' => $stf->id,
                        'leave_type_id' => $clt->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'allocated_days' => $clt->days_per_year,
                        'used_days' => rand(0, 3),
                    ]
                );
            }
        }

        // Create sample pending and approved leave applications
        if (count($createdStaff) >= 3) {
            \App\Models\LeaveApplication::firstOrCreate(
                [
                    'staff_id' => $createdStaff[4]->id,
                    'start_date' => $today,
                    'end_date' => $today,
                ],
                [
                    'leave_type_id' => $createdLeaveTypes[0]->id,
                    'days_count' => 1.0,
                    'reason' => 'Family function at native place in Trichy',
                    'status' => 'approved',
                    'approved_by' => $usersMap['principal']->id,
                    'remarks' => 'Approved by Principal',
                ]
            );

            \App\Models\LeaveApplication::firstOrCreate(
                [
                    'staff_id' => $createdStaff[2]->id,
                    'start_date' => \Carbon\Carbon::parse($today)->addDays(2)->toDateString(),
                    'end_date' => \Carbon\Carbon::parse($today)->addDays(3)->toDateString(),
                ],
                [
                    'leave_type_id' => $createdLeaveTypes[1]->id,
                    'days_count' => 2.0,
                    'reason' => 'Scheduled medical dental checkup',
                    'status' => 'pending',
                ]
            );
        }

        // 5c. Initial Monthly Payroll (October 2026)
        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
        $payrollService = app(\App\Services\PayrollService::class);
        $payrollService->generateMonthlyPayroll($currentMonth);

        // 6. Realistic Students (~308 students across 28 sections)
        $firstNames = ['Aarav', 'Dharun', 'Karthik', 'Sanjay', 'Vignesh', 'Manoj', 'Naveen', 'Surya', 'Praveen', 'Rohit', 'Ananya', 'Divya', 'Harini', 'Janani', 'Keerthana', 'Meera', 'Pooja', 'Rithika', 'Sneha', 'Varsha', 'Ajay', 'Balaji', 'Chandru', 'Dinesh', 'Ganesh', 'Hari', 'Iniyan', 'Jeeva', 'Kamal', 'Logesh'];
        $lastNames = ['Santhosh', 'Raman', 'Kumar', 'Sundaram', 'Murugan', 'Velan', 'Subramaniam', 'Natarajan', 'Selvam', 'Pandian', 'Balan', 'Chandran', 'Dharmalingam', 'Govindan', 'Ilangovan'];

        $studentCount = 0;
        foreach ($allSections as $section) {
            $countPerSection = 11; // 28 sections * 11 = 308 students
            for ($i = 1; $i <= $countPerSection; $i++) {
                $studentCount++;
                $fName = $firstNames[($studentCount + $i) % count($firstNames)];
                $lName = $lastNames[($studentCount * 3) % count($lastNames)];
                $gender = ($studentCount % 2 === 0) ? 'female' : 'male';
                $admNo = 'ADM-2026-' . str_pad($studentCount, 4, '0', STR_PAD_LEFT);

                // Assign the demo parent to student 42 (Aarav Santhosh)
                $parentId = ($studentCount === 42) ? $usersMap['parent']->id : null;

                $student = Student::firstOrCreate(
                    ['admission_no' => $admNo],
                    [
                        'first_name' => ($studentCount === 42) ? 'Aarav' : $fName,
                        'last_name' => ($studentCount === 42) ? 'Santhosh' : $lName,
                        'gender' => $gender,
                        'dob' => Carbon::today()->subYears(18 - ($section->school_class_id))->subDays(rand(10, 300)),
                        'school_class_id' => $section->school_class_id,
                        'section_id' => $section->id,
                        'parent_id' => $parentId,
                        'roll_number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                        'admission_date' => '2026-04-10',
                        'status' => 'active',
                        'blood_group' => ['O+', 'A+', 'B+', 'AB+'][rand(0, 3)],
                        'emergency_contact' => '+91 98421 ' . str_pad($studentCount, 5, '0', STR_PAD_LEFT),
                    ]
                );

                // Student Attendance Today (95.5% present rate)
                $attStatus = ($studentCount % 22 === 0) ? 'absent' : 'present';
                StudentAttendance::firstOrCreate(
                    ['student_id' => $student->id, 'date' => $today],
                    [
                        'status' => $attStatus,
                        'remarks' => $attStatus === 'absent' ? 'Medical leave requested' : null,
                    ]
                );
            }
        }

        // 7. Store Items
        $storeCatalog = [
            ['name' => 'Regular Uniform Shirt & Pant (Size 32)', 'category' => 'Uniform', 'code' => 'UNIF-32', 'p' => 600, 's' => 850, 'stock' => 4, 'alert' => 10],
            ['name' => 'Regular Uniform Shirt & Pant (Size 34)', 'category' => 'Uniform', 'code' => 'UNIF-34', 'p' => 650, 's' => 900, 'stock' => 18, 'alert' => 10],
            ['name' => 'Sports House T-Shirt Emerald (Size M)', 'category' => 'Uniform', 'code' => 'SPT-EM-M', 'p' => 220, 's' => 350, 'stock' => 5, 'alert' => 12],
            ['name' => 'State Board Mathematics Reader 10th', 'category' => 'Book', 'code' => 'BK-MAT-10', 'p' => 90, 's' => 140, 'stock' => 3, 'alert' => 15],
            ['name' => 'State Board Science Reader 10th', 'category' => 'Book', 'code' => 'BK-SCI-10', 'p' => 95, 's' => 150, 'stock' => 42, 'alert' => 15],
            ['name' => 'State Board Tamil Reader 10th', 'category' => 'Book', 'code' => 'BK-TAM-10', 'p' => 80, 's' => 120, 'stock' => 55, 'alert' => 15],
            ['name' => '192-Page Ruled Long Notebook', 'category' => 'Notebook', 'code' => 'NB-192-R', 'p' => 42, 's' => 65, 'stock' => 180, 'alert' => 30],
            ['name' => '192-Page Unruled Long Notebook', 'category' => 'Notebook', 'code' => 'NB-192-U', 'p' => 42, 's' => 65, 'stock' => 165, 'alert' => 30],
            ['name' => 'School Crest Premium Backpack', 'category' => 'Bag', 'code' => 'BAG-CR-01', 'p' => 450, 's' => 750, 'stock' => 24, 'alert' => 8],
            ['name' => 'Camlin Geometry Mathematical Box', 'category' => 'Stationery', 'code' => 'STN-GEO-01', 'p' => 95, 's' => 135, 'stock' => 60, 'alert' => 15],
        ];

        foreach ($storeCatalog as $sc) {
            StoreItem::firstOrCreate(
                ['item_code' => $sc['code']],
                [
                    'name' => $sc['name'],
                    'category' => $sc['category'],
                    'purchase_price' => $sc['p'],
                    'selling_price' => $sc['s'],
                    'stock_quantity' => $sc['stock'],
                    'alert_threshold' => $sc['alert'],
                ]
            );
        }

        // 8. Fee Receipts Today & This Month
        $sampleStudents = Student::limit(15)->get();
        foreach ($sampleStudents as $idx => $st) {
            $recDate = ($idx < 4) ? $today : Carbon::today()->subDays($idx * 2)->toDateString();
            $amt = match ($idx % 3) {
                0 => 12000.00,
                1 => 10000.00,
                default => 8000.00,
            };

            $receipt = FeeReceipt::firstOrCreate(
                ['receipt_no' => 'SCH/2026-27/REC-' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT)],
                [
                    'student_id' => $st->id,
                    'academic_year_id' => $academicYear->id,
                    'amount_paid' => $amt,
                    'payment_method' => ['Cash', 'UPI', 'Bank Transfer'][$idx % 3],
                    'receipt_date' => $recDate,
                    'collected_by' => $usersMap['accountant']->id,
                    'notes' => 'Term 1 / Term 2 Tuition Fee',
                ]
            );

            \App\Models\FeeReceiptItem::firstOrCreate(
                [
                    'fee_receipt_id' => $receipt->id,
                    'description' => 'Tuition & Academic Term Fee (Term 1)',
                ],
                [
                    'amount' => $amt,
                ]
            );
        }

        // 8a. Class Fee Structures Master (LKG to 12)
        $allClasses = SchoolClass::all();
        $createdFeeStructures = [];
        foreach ($allClasses as $cls) {
            $baseTuition = match (true) {
                str_contains($cls->name, '11') || str_contains($cls->name, '12') => 18000,
                str_contains($cls->name, '9') || str_contains($cls->name, '10') => 15000,
                str_contains($cls->name, '6') || str_contains($cls->name, '7') || str_contains($cls->name, '8') => 12000,
                str_contains($cls->name, '1') || str_contains($cls->name, '2') || str_contains($cls->name, '3') || str_contains($cls->name, '4') || str_contains($cls->name, '5') => 10000,
                default => 8000, // LKG, UKG
            };

            $structs = [
                ['name' => 'Tuition & Academic Term Fee', 'term' => 'Term 1', 'amount' => $baseTuition, 'due_date' => '2026-06-15'],
                ['name' => 'Laboratory & Computer Facility Fee', 'term' => 'Term 1', 'amount' => 3500, 'due_date' => '2026-06-30'],
                ['name' => 'Tuition & Mid-Term Exam Fee', 'term' => 'Term 2', 'amount' => $baseTuition, 'due_date' => '2026-10-15'],
                ['name' => 'Sports & Extracurricular Activities', 'term' => 'Term 2', 'amount' => 2500, 'due_date' => '2026-10-31'],
                ['name' => 'Tuition & Annual Board Evaluation', 'term' => 'Term 3', 'amount' => $baseTuition, 'due_date' => '2027-01-15'],
            ];

            foreach ($structs as $st) {
                $createdFeeStructures[] = \App\Models\FeeStructure::firstOrCreate(
                    [
                        'school_class_id' => $cls->id,
                        'academic_year_id' => $academicYear->id,
                        'name' => $st['name'],
                        'term' => $st['term'],
                    ],
                    [
                        'amount' => $st['amount'],
                        'due_date' => $st['due_date'],
                    ]
                );
            }
        }

        // 8b. Sample Concessions
        $concessionStudents = Student::take(5)->get();
        if ($concessionStudents->count() >= 3) {
            \App\Models\FeeConcession::firstOrCreate(
                [
                    'student_id' => $concessionStudents[0]->id,
                    'academic_year_id' => $academicYear->id,
                    'title' => 'Sibling Concession (25%)',
                ],
                [
                    'concession_type' => 'percentage',
                    'amount' => 25,
                    'reason' => 'Second sibling enrolled in Class 4',
                    'approved_by' => $usersMap['chairman']->id,
                ]
            );

            \App\Models\FeeConcession::firstOrCreate(
                [
                    'student_id' => $concessionStudents[1]->id,
                    'academic_year_id' => $academicYear->id,
                    'title' => 'Merit Scholarship',
                ],
                [
                    'concession_type' => 'fixed',
                    'amount' => 10000,
                    'reason' => 'District topper in State Science Talent Search',
                    'approved_by' => $usersMap['principal']->id,
                ]
            );
        }

        // 8c. Fee Allocations for Students
        $allStudents = Student::all();
        foreach ($allStudents as $idx => $ast) {
            $classStructures = collect($createdFeeStructures)->where('school_class_id', $ast->school_class_id);
            foreach ($classStructures as $cStruct) {
                $hasConcession = ($idx === 0);
                $concAmt = $hasConcession ? round($cStruct->amount * 0.25, 2) : 0;
                $netAmt = max(0, $cStruct->amount - $concAmt);

                $isPaid = ($cStruct->term === 'Term 1' && $idx < 220);
                $isTerm2Paid = ($cStruct->term === 'Term 2' && $idx < 100);
                $paidAmt = $isPaid ? $netAmt : ($isTerm2Paid ? $netAmt : 0);
                $balDue = max(0, $netAmt - $paidAmt);

                \App\Models\FeeAllocation::firstOrCreate(
                    [
                        'student_id' => $ast->id,
                        'fee_structure_id' => $cStruct->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'amount' => $cStruct->amount,
                        'concession_amount' => $concAmt,
                        'net_amount' => $netAmt,
                        'paid_amount' => $paidAmt,
                        'balance_due' => $balDue,
                        'status' => $balDue <= 0.01 ? 'paid' : ($paidAmt > 0 ? 'partial' : 'pending'),
                        'due_date' => $cStruct->due_date,
                    ]
                );
            }
        }

        // 8d. Store Sales POS Invoices
        $storeItemsList = StoreItem::all();
        if ($storeItemsList->count() >= 4) {
            $salesData = [
                ['name' => 'Aarav Santhosh', 'type' => 'student', 'pm' => 'UPI', 'date' => $today, 'items' => [[0, 1], [6, 2], [9, 1]]],
                ['name' => 'Kavitha R. (Parent)', 'type' => 'parent', 'pm' => 'Cash', 'date' => $today, 'items' => [[1, 2], [7, 4]]],
                ['name' => 'S. Meenakshi (Teacher)', 'type' => 'staff', 'pm' => 'UPI', 'date' => Carbon::today()->subDays(1)->toDateString(), 'items' => [[6, 5], [9, 2]]],
                ['name' => 'Vignesh P.', 'type' => 'student', 'pm' => 'Cash', 'date' => Carbon::today()->subDays(2)->toDateString(), 'items' => [[2, 1], [8, 1]]],
                ['name' => 'M. Karthik (Parent)', 'type' => 'parent', 'pm' => 'Card', 'date' => Carbon::today()->subDays(3)->toDateString(), 'items' => [[0, 1], [3, 1], [4, 1]]],
            ];

            foreach ($salesData as $sIdx => $sd) {
                $billNo = sprintf("SCH/26-27/STR-%05d", $sIdx + 1);
                $subtotal = 0;
                $cost = 0;
                $profit = 0;
                $saleRows = [];

                foreach ($sd['items'] as $itemPair) {
                    $item = $storeItemsList[$itemPair[0]] ?? $storeItemsList->first();
                    $qty = $itemPair[1];
                    $rowSubtotal = (float)$item->selling_price * $qty;
                    $rowCost = (float)$item->purchase_price * $qty;
                    $rowProfit = $rowSubtotal - $rowCost;

                    $subtotal += $rowSubtotal;
                    $cost += $rowCost;
                    $profit += $rowProfit;

                    $saleRows[] = [
                        'store_item_id' => $item->id,
                        'quantity' => $qty,
                        'cost_price' => $item->purchase_price,
                        'unit_price' => $item->selling_price,
                        'subtotal' => $rowSubtotal,
                        'profit' => $rowProfit,
                    ];
                }

                $sale = \App\Models\StoreSale::firstOrCreate(
                    ['bill_no' => $billNo],
                    [
                        'customer_name' => $sd['name'],
                        'customer_type' => $sd['type'],
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'total_amount' => $subtotal,
                        'cost_amount' => $cost,
                        'profit_amount' => $profit,
                        'payment_method' => $sd['pm'],
                        'sale_date' => $sd['date'],
                        'cashier_id' => $usersMap['accountant']->id,
                        'notes' => 'Uniforms, notebooks & stationery purchase',
                    ]
                );

                foreach ($saleRows as $sr) {
                    \App\Models\StoreSaleItem::firstOrCreate(
                        [
                            'store_sale_id' => $sale->id,
                            'store_item_id' => $sr['store_item_id'],
                        ],
                        $sr
                    );
                }
            }
        }

        // 9. Ledger Expenses This Month
        $expensesCatalog = [
            ['cat' => 'Salaries', 'title' => 'Monthly Teaching Staff Salary Advance', 'vendor' => 'Staff Payroll Account', 'bill' => 'SAL-2026-10', 'amt' => 520000, 'date' => Carbon::today()->subDays(5)->toDateString()],
            ['cat' => 'Maintenance', 'title' => 'Campus RO Water Plant Servicing & Filters', 'vendor' => 'Pudukkottai Aqua Tech', 'bill' => 'INV-4821', 'amt' => 18500, 'date' => Carbon::today()->subDays(3)->toDateString()],
            ['cat' => 'Utilities', 'title' => 'TANGEDCO Electricity Bill (Oct 2026)', 'vendor' => 'Tamil Nadu Electricity Board', 'bill' => 'EB-984210', 'amt' => 42300, 'date' => Carbon::today()->subDays(7)->toDateString()],
            ['cat' => 'Stationery', 'title' => 'Term 2 Exam Paper & Printing Reams', 'vendor' => 'Sri Murugan Paper Mart', 'bill' => 'PM-3312', 'amt' => 28400, 'date' => $today],
            ['cat' => 'Transport', 'title' => 'School Buses Diesel & Maintenance', 'vendor' => 'Indian Oil Corporation Filling Stn', 'bill' => 'IOC-7891', 'amt' => 64000, 'date' => Carbon::today()->subDays(2)->toDateString()],
        ];

        foreach ($expensesCatalog as $ec) {
            Expense::firstOrCreate(
                ['bill_no' => $ec['bill']],
                [
                    'category' => $ec['cat'],
                    'title' => $ec['title'],
                    'vendor_name' => $ec['vendor'],
                    'amount' => $ec['amt'],
                    'payment_date' => $ec['date'],
                    'payment_method' => 'Bank Transfer',
                    'approved_by' => $usersMap['principal']->id,
                    'notes' => 'Approved by Principal',
                ]
            );
        }

        // 10. Academics: Subjects, Timetables, Exams, Marks & Homework
        $subjectsList = [
            ['name' => 'English Language & Literature', 'code' => 'ENG', 'type' => 'theory', 'department' => 'English Language'],
            ['name' => 'Tamil Language (General)', 'code' => 'TAM', 'type' => 'theory', 'department' => 'Tamil Language'],
            ['name' => 'Mathematics', 'code' => 'MAT', 'type' => 'theory', 'department' => 'Mathematics'],
            ['name' => 'General Science', 'code' => 'SCI', 'type' => 'both', 'department' => 'Science'],
            ['name' => 'Social Science & Civics', 'code' => 'SOC', 'type' => 'theory', 'department' => 'Social Studies'],
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'both', 'department' => 'Science'],
            ['name' => 'Chemistry', 'code' => 'CHE', 'type' => 'both', 'department' => 'Science'],
            ['name' => 'Biology & Botany', 'code' => 'BIO', 'type' => 'both', 'department' => 'Science'],
            ['name' => 'Computer Science & Python', 'code' => 'CSC', 'type' => 'both', 'department' => 'Computer Science'],
        ];

        $createdSubjects = [];
        foreach ($subjectsList as $s) {
            $createdSubjects[$s['code']] = \App\Models\Subject::firstOrCreate(['code' => $s['code']], $s);
        }

        // Timetable Slots for Class 10 (Section A & B)
        $class10 = \App\Models\SchoolClass::where('name', 'like', '%10%')->first() ?? $allClasses->first();
        $sections10 = \App\Models\Section::where('school_class_id', $class10->id)->get();

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $subjectCodes = ['ENG', 'TAM', 'MAT', 'SCI', 'SOC', 'MAT', 'CSC', 'ENG'];

        $teachingStaff = \App\Models\Staff::where('department', 'Teaching')->orWhere('department', 'Mathematics')->orWhere('department', 'Science')->get();
        if ($teachingStaff->isEmpty()) {
            $teachingStaff = \App\Models\Staff::all();
        }

        foreach ($sections10 as $sIdx => $sec) {
            foreach ($days as $dIdx => $day) {
                for ($p = 1; $p <= 8; $p++) {
                    $code = $subjectCodes[($p - 1 + $dIdx) % count($subjectCodes)];
                    $subj = $createdSubjects[$code] ?? $createdSubjects['ENG'];
                    // Distribute teachers so there is no teacher clash between Section A and B
                    $tIdx = ($p + $dIdx + ($sIdx * 3)) % $teachingStaff->count();
                    $assignedTeacher = $teachingStaff[$tIdx];

                    \App\Models\Timetable::firstOrCreate(
                        [
                            'school_class_id' => $class10->id,
                            'section_id' => $sec->id,
                            'day_of_week' => $day,
                            'period_number' => $p,
                        ],
                        [
                            'subject_id' => $subj->id,
                            'staff_id' => $assignedTeacher->id,
                            'start_time' => '09:00:00',
                            'end_time' => '09:45:00',
                            'room_no' => 'Room ' . ($sIdx === 0 ? '201' : '202'),
                        ]
                    );
                }
            }
        }

        // Exams & CCE Schedules
        $quarterlyExam = \App\Models\Exam::firstOrCreate(
            ['name' => 'Quarterly Examination 2026-2027'],
            [
                'exam_type' => 'quarterly',
                'academic_year_id' => $academicYear->id,
                'start_date' => '2026-09-15',
                'end_date' => '2026-09-25',
                'status' => 'completed',
            ]
        );

        $examSubjects = ['ENG', 'TAM', 'MAT', 'SCI', 'SOC'];
        $createdSchedules = [];
        foreach ($examSubjects as $eIdx => $eCode) {
            if (isset($createdSubjects[$eCode])) {
                $sch = \App\Models\ExamSchedule::firstOrCreate(
                    [
                        'exam_id' => $quarterlyExam->id,
                        'school_class_id' => $class10->id,
                        'subject_id' => $createdSubjects[$eCode]->id,
                    ],
                    [
                        'exam_date' => \Carbon\Carbon::parse('2026-09-15')->addDays($eIdx * 2)->toDateString(),
                        'start_time' => '09:30:00',
                        'end_time' => '12:30:00',
                        'max_marks' => 100,
                        'pass_marks' => 35,
                    ]
                );
                $createdSchedules[] = $sch;
            }
        }

        // Seed Marks for Class 10 Students
        $reportService = app(\App\Services\ReportService::class);
        $class10Students = \App\Models\Student::where('school_class_id', $class10->id)->get();
        foreach ($class10Students as $st) {
            foreach ($createdSchedules as $sch) {
                $obtained = rand(65, 96);
                $gradeInfo = $reportService->calculateGrade($obtained, 100);

                \App\Models\Mark::firstOrCreate(
                    [
                        'exam_schedule_id' => $sch->id,
                        'student_id' => $st->id,
                    ],
                    [
                        'marks_obtained' => $obtained,
                        'grade' => $gradeInfo['grade'],
                        'grade_point' => $gradeInfo['point'],
                        'is_absent' => false,
                        'remarks' => $gradeInfo['remark'],
                    ]
                );
            }
        }

        // Seed Homework
        if ($sections10->isNotEmpty() && $teachingStaff->isNotEmpty()) {
            \App\Models\Homework::firstOrCreate(
                [
                    'school_class_id' => $class10->id,
                    'section_id' => $sections10->first()->id,
                    'title' => 'Quadratic Equations Exercise 3.2',
                ],
                [
                    'subject_id' => $createdSubjects['MAT']->id,
                    'staff_id' => $teachingStaff->first()->id,
                    'description' => 'Solve problems 1 to 15 from Chapter 3 on Quadratic Factorisation in State Board textbook.',
                    'assigned_date' => $today,
                    'due_date' => \Carbon\Carbon::parse($today)->addDays(2)->toDateString(),
                    'status' => 'active',
                ]
            );

            \App\Models\Homework::firstOrCreate(
                [
                    'school_class_id' => $class10->id,
                    'section_id' => $sections10->first()->id,
                    'title' => 'Thirukkural Explanation Essays (Arathupal)',
                ],
                [
                    'subject_id' => $createdSubjects['TAM']->id,
                    'staff_id' => $teachingStaff->first()->id,
                    'description' => 'Write a 2-page essay analyzing the moral virtues in Thirukkural chapters 1 through 5.',
                    'assigned_date' => $today,
                    'due_date' => \Carbon\Carbon::parse($today)->addDays(3)->toDateString(),
                    'status' => 'active',
                ]
            );
        }

        // 11. Communication & Records (Calendar, Announcements, PTM, Parent Notes, Certificates)
        $adminUser = User::where('role', 'admin')->first() ?? User::first();
        $principalUser = User::where('role', 'principal')->first() ?? $adminUser;
        $parentUser = User::where('role', 'parent')->first();
        $firstStudent = Student::first();
        $secondStudent = Student::skip(1)->first() ?? $firstStudent;

        // Calendar Events
        $calendarEvents = [
            [
                'title' => 'Independence Day Celebrations & Flag Hoisting',
                'event_type' => 'function',
                'start_date' => '2026-08-15',
                'end_date' => '2026-08-15',
                'description' => 'Flag hoisting at 08:30 AM followed by student march-past and cultural events.',
                'is_holiday' => false,
                'target_audience' => 'all',
            ],
            [
                'title' => 'Gandhi Jayanti Public Holiday',
                'event_type' => 'holiday',
                'start_date' => '2026-10-02',
                'end_date' => '2026-10-02',
                'description' => 'State and National Holiday. Campus closed.',
                'is_holiday' => true,
                'target_audience' => 'all',
            ],
            [
                'title' => 'Annual Sports Meet 2026',
                'event_type' => 'sports',
                'start_date' => '2026-10-24',
                'end_date' => '2026-10-25',
                'description' => 'Inter-house athletic track & field events at District Stadium Pudukkottai.',
                'is_holiday' => false,
                'target_audience' => 'students',
            ],
            [
                'title' => 'Deepavali Festival Vacation',
                'event_type' => 'holiday',
                'start_date' => '2026-11-07',
                'end_date' => '2026-11-10',
                'description' => 'Deepavali festival holidays as declared by Tamil Nadu School Education Dept.',
                'is_holiday' => true,
                'target_audience' => 'all',
            ],
            [
                'title' => 'Science & Technology Innovation Exhibition',
                'event_type' => 'academic',
                'start_date' => '2026-12-15',
                'end_date' => '2026-12-16',
                'description' => 'Working model demonstrations by classes 6 to 12. Parents and public invited.',
                'is_holiday' => false,
                'target_audience' => 'all',
            ],
            [
                'title' => 'Pongal Vacation',
                'event_type' => 'holiday',
                'start_date' => '2027-01-14',
                'end_date' => '2027-01-17',
                'description' => 'Thai Pongal and Uzhavar Thirunal state holidays.',
                'is_holiday' => true,
                'target_audience' => 'all',
            ],
        ];

        foreach ($calendarEvents as $evt) {
            CalendarEvent::firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'title' => $evt['title'],
                    'start_date' => $evt['start_date'],
                ],
                array_merge($evt, ['created_by' => $principalUser?->id])
            );
        }

        // Announcements
        $announcements = [
            [
                'title' => 'Term 1 Examinations Time Table & Preparation Guidelines',
                'content' => 'The Term 1 CCE written examinations will commence from September 15. All students are advised to revise lessons and bring necessary geometry and writing instruments. Hall tickets will be issued by the respective class teachers.',
                'category' => 'academic',
                'target_type' => 'all',
                'publish_date' => $today,
                'is_pinned' => true,
            ],
            [
                'title' => 'Staff Meeting: Evaluation Rubrics for State Board CCE',
                'content' => 'All teaching faculty are requested to assemble in the AV Seminar Hall at 04:00 PM today for alignment on FA-A, FA-B, and Summative Assessment grade distributions.',
                'category' => 'general',
                'target_type' => 'staff',
                'publish_date' => $today,
                'is_pinned' => false,
            ],
            [
                'title' => 'Special Coaching Classes for Class 10 Students',
                'content' => 'Mandatory Saturday remedial coaching classes in Mathematics and Physical Sciences for 10th standard pupils will be held from 09:00 AM to 12:30 PM starting this weekend.',
                'category' => 'academic',
                'target_type' => 'class',
                'school_class_id' => $class10->id,
                'publish_date' => $today,
                'is_pinned' => false,
            ],
        ];

        foreach ($announcements as $ann) {
            Announcement::firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'title' => $ann['title'],
                ],
                array_merge($ann, ['author_id' => $principalUser?->id])
            );
        }

        // PTM Schedules
        if ($sections10->isNotEmpty()) {
            PtmSchedule::firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'school_class_id' => $class10->id,
                    'section_id' => $sections10->first()->id,
                    'meeting_date' => \Carbon\Carbon::parse($today)->addDays(5)->toDateString(),
                ],
                [
                    'start_time' => '09:30:00',
                    'end_time' => '13:00:00',
                    'venue' => 'Room 112 (10-A Classroom)',
                    'agenda' => 'Review of Unit Test marks, board exam strategy, and individual student progress counseling with parents.',
                    'status' => 'scheduled',
                    'created_by' => $adminUser?->id,
                ]
            );
        }

        // Parent Notes
        if ($firstStudent && $parentUser) {
            ParentNote::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'parent_id' => $parentUser->id,
                    'subject' => 'Enquiry regarding School Bus Route #4 Pickup Time',
                ],
                [
                    'staff_id' => $teachingStaff->first()?->id,
                    'message' => 'Respected Class Teacher, kindly let us know if the morning pickup timing at Alangudi Bus Stand remains 07:45 AM or has changed due to road repair works.',
                    'reply' => 'Dear Parent, the route pickup remains at 07:50 AM sharp. The driver Mr. Murugan will wait for 3 minutes at the designated stop.',
                    'replied_at' => Carbon::now()->subHours(4),
                    'status' => 'replied',
                ]
            );

            ParentNote::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'parent_id' => $parentUser->id,
                    'subject' => 'Request for special permission for upcoming inter-school chess tournament',
                ],
                [
                    'staff_id' => null,
                    'message' => 'My ward has been selected to represent Pudukkottai District in the State Chess Championship on Friday. Kindly grant attendance on duty.',
                    'reply' => null,
                    'replied_at' => null,
                    'status' => 'pending',
                ]
            );
        }

        // Sample Official Certificates
        if ($firstStudent) {
            Certificate::firstOrCreate(
                ['certificate_no' => 'SCH/26-27/BON-00001'],
                [
                    'student_id' => $firstStudent->id,
                    'academic_year_id' => $academicYear->id,
                    'certificate_type' => 'bonafide',
                    'issue_date' => $today,
                    'reason' => 'Passport Application & Regional Verification',
                    'details' => ['purpose' => 'Passport Application'],
                    'issued_by' => $adminUser?->id,
                ]
            );
        }

        if ($secondStudent) {
            Certificate::firstOrCreate(
                ['certificate_no' => 'SCH/26-27/TC-00001'],
                [
                    'student_id' => $secondStudent->id,
                    'academic_year_id' => $academicYear->id,
                    'certificate_type' => 'transfer_certificate',
                    'issue_date' => $today,
                    'reason' => 'Family Relocation to Chennai',
                    'details' => [
                        'leaving_date' => $today,
                        'conduct' => 'EXCELLENT',
                        'fee_dues_cleared' => 'Yes',
                        'reason_for_leaving' => 'Family Relocation to Chennai',
                        'identification_marks' => '1. A mole on the right cheek; 2. A birthmark on left shoulder',
                    ],
                    'issued_by' => $adminUser?->id,
                ]
            );
        }

        // 12. Default System Settings
        $settings = [
            'school_name' => 'Test Subject Higher Secondary School',
            'school_board' => 'Tamil Nadu State Board',
            'school_affiliation_no' => 'TN-EDU-2026-4821',
            'school_location' => 'Pudukkottai, Tamil Nadu',
            'currency_symbol' => '₹',
            'app_integration_enabled' => 'false',
            'biometric_api_secret' => 'BIO-SEC-9842-TEST-KEY',
            'fee_terms' => '3',
            'receipt_prefix_fees' => 'SCH/2026-27/REC-',
            'receipt_prefix_store' => 'SCH/2026-27/STR-',
        ];

        foreach ($settings as $key => $val) {
            Setting::set($key, $val, 'system');
        }
    }
}
