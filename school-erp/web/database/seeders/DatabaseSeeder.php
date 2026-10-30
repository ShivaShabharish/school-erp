<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\FeeReceipt;
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

            FeeReceipt::firstOrCreate(
                ['receipt_no' => 'SCH/2026-27/REC-' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT)],
                [
                    'student_id' => $st->id,
                    'amount_paid' => $amt,
                    'payment_method' => ['Cash', 'UPI', 'Bank Transfer'][$idx % 3],
                    'receipt_date' => $recDate,
                    'collected_by' => $usersMap['accountant']->id,
                    'notes' => 'Term 1 / Term 2 Tuition Fee',
                ]
            );
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

        // 10. Default System Settings
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
