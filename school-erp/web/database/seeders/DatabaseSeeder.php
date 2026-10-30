<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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

        foreach ($classes as $className => $seq) {
            $class = SchoolClass::firstOrCreate(
                ['name' => $className],
                ['order_sequence' => $seq]
            );

            Section::firstOrCreate(
                ['school_class_id' => $class->id, 'name' => 'A'],
                ['room_no' => 'Room ' . (100 + $seq), 'max_capacity' => 40]
            );

            Section::firstOrCreate(
                ['school_class_id' => $class->id, 'name' => 'B'],
                ['room_no' => 'Room ' . (200 + $seq), 'max_capacity' => 40]
            );
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

        foreach ($demoUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Password@123'),
                    'role' => $userData['role'],
                    'phone' => $userData['phone'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Default Settings
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
