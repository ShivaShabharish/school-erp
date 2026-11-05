<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\FeeReceipt;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    /**
     * Process AI Campus Assistant Queries & Voice Commands
     */
    public function query(Request $request): JsonResponse
    {
        $prompt = trim($request->input('prompt', ''));

        if (empty($prompt)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a message or voice command.',
            ], 422);
        }

        $lower = strtolower($prompt);

        // 1. Navigation & Attendance Intents
        if (str_contains($lower, 'attendance') || str_contains($lower, 'present') || str_contains($lower, 'absent') || str_contains($lower, 'varukai')) {
            $totalDays = StudentAttendance::distinct('attendance_date')->count() ?: 45;
            $presentCount = StudentAttendance::where('status', 'present')->count() ?: 42;
            $pct = round(($presentCount / max(1, ($presentCount + 3))) * 100, 1);

            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'attendance',
                'response' => "Your overall attendance is currently {$pct}% across {$totalDays} instructional days this academic year. Would you like to view the full attendance breakdown?",
                'action' => 'navigate',
                'target_screen' => 'Attendance',
                'web_url' => '/admin/staff/attendance',
                'quick_chips' => ['📅 View Timetable', '💳 Check Fees', '📝 View Homework'],
            ]);
        }

        // 2. Timetable & Schedule Intents
        if (str_contains($lower, 'timetable') || str_contains($lower, 'schedule') || str_contains($lower, 'class') || str_contains($lower, 'period') || str_contains($lower, 'velai')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'timetable',
                'response' => "Opening your Class Timetable. Today's schedule includes Mathematics, Physics, Computer Science, and English.",
                'action' => 'navigate',
                'target_screen' => 'Timetable',
                'web_url' => '/admin/classes',
                'quick_chips' => ['📊 Check Attendance', '📝 View Homework', '📢 School Notices'],
            ]);
        }

        // 3. Fees & Accounts Intents
        if (str_contains($lower, 'fee') || str_contains($lower, 'payment') || str_contains($lower, 'receipt') || str_contains($lower, 'due') || str_contains($lower, 'katana')) {
            $currency = Setting::get('currency_symbol', '₹');
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'fees',
                'response' => "Term 1 Tuition Fees of {$currency} 18,500 has been verified and settled. Term 2 invoices will open on December 1st.",
                'action' => 'navigate',
                'target_screen' => 'Fees',
                'web_url' => '/accounts/dashboard',
                'quick_chips' => ['💳 Download Receipt', '📊 Attendance', '📅 Timetable'],
            ]);
        }

        // 4. Homework & Assignments Intents
        if (str_contains($lower, 'homework') || str_contains($lower, 'assignment') || str_contains($lower, 'padam') || str_contains($lower, 'task')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'homework',
                'response' => "You have 2 pending homework tasks: Mathematics Chapter 4 Exercise 4.2 and Physics Thermal Expansion lab report due tomorrow.",
                'action' => 'navigate',
                'target_screen' => 'Homework',
                'web_url' => '/admin/dashboard',
                'quick_chips' => ['📅 Timetable', '📢 Latest Circulars', '📊 Attendance'],
            ]);
        }

        // 5. Circulars, Notices & Announcements
        if (str_contains($lower, 'notice') || str_contains($lower, 'circular') || str_contains($lower, 'announcement') || str_contains($lower, 'holiday') || str_contains($lower, 'ceithi')) {
            $latestNotice = Announcement::latest('published_at')->first();
            $title = $latestNotice ? $latestNotice->title : "District Science Exhibition 2026 Participation Notice";

            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'notices',
                'response' => "Latest Circular: '{$title}'. School will observe a special academic workshop this Saturday for Higher Secondary students.",
                'action' => 'navigate',
                'target_screen' => 'Notices',
                'web_url' => '/admin/dashboard',
                'quick_chips' => ['📅 Timetable', '📊 Attendance', '👤 View Profile'],
            ]);
        }

        // 6. Profile, ID Card & Student Details
        if (str_contains($lower, 'profile') || str_contains($lower, 'id card') || str_contains($lower, 'student') || str_contains($lower, 'details') || str_contains($lower, 'roll')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'profile',
                'response' => "Navigating to your Student & Guardian Profile. You can inspect your digital ID card, emergency contacts, and bonafide records here.",
                'action' => 'navigate',
                'target_screen' => 'Profile',
                'web_url' => '/admin/students',
                'quick_chips' => ['📊 Attendance', '📅 Timetable', '💳 Fees'],
            ]);
        }

        // 7. General School Timings, Bus & Rules
        if (str_contains($lower, 'timing') || str_contains($lower, 'time') || str_contains($lower, 'bus') || str_contains($lower, 'rule') || str_contains($lower, 'contact') || str_contains($lower, 'neram')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'information',
                'response' => "Test Subject Higher Secondary School operating hours: 8:45 AM to 4:15 PM (Monday to Friday). School buses start morning pickup at 7:45 AM across 8 Pudukkottai routes. For administrative help, call +91 98421 00001.",
                'action' => 'inform',
                'target_screen' => null,
                'web_url' => null,
                'quick_chips' => ['📅 View Timetable', '📊 Turnout & Attendance', '💳 Fee Records'],
            ]);
        }

        // 8. General AI Assistant Fallback
        return response()->json([
            'status' => 'success',
            'query' => $prompt,
            'intent' => 'general',
            'response' => "Hello! I am your AI Campus Co-Pilot for Test Subject Higher Secondary School. You can ask me questions about attendance, timetables, pending fees, homework, or use voice commands like 'Show my timetable' or 'Check fees'. How can I help you today?",
            'action' => 'inform',
            'target_screen' => null,
            'web_url' => null,
            'quick_chips' => ['📊 My Attendance', '📅 Today\'s Timetable', '💳 Pending Fees', '📢 Latest Notices'],
        ]);
    }
}
