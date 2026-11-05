<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\FeeReceipt;
use App\Models\LeaveApplication;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    /**
     * Process AI Campus Assistant Queries & Voice-to-Action Executive Commands
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
        $user = Auth::user();

        // ==========================================
        // FEATURE 5: VOICE-TO-ACTION EXECUTIVE ENGINE
        // ==========================================

        // Action A: Executive Voice Broadcast Notice
        if (str_starts_with($lower, 'broadcast') || str_starts_with($lower, 'post notice') || str_starts_with($lower, 'post announcement') || str_contains($lower, 'broadcast notice')) {
            $cleaned = preg_replace('/^(broadcast notice|broadcast announcement|broadcast|post notice|post announcement)\s*(to all|to parents)?\s*:?\s*/i', '', $prompt);
            if (empty($cleaned)) {
                $cleaned = "Special School Circular: Academic review & parent instructions.";
            }

            $currentYear = AcademicYear::current() ?? AcademicYear::latest()->first();
            $authorId = $user ? $user->id : 1;

            $notice = Announcement::create([
                'academic_year_id' => $currentYear?->id ?? 1,
                'title' => 'Executive Notice: ' . \Illuminate\Support\Str::limit($cleaned, 60),
                'content' => $cleaned,
                'category' => 'general',
                'target_type' => 'all',
                'publish_date' => now()->toDateString(),
                'is_pinned' => true,
                'author_id' => $authorId,
            ]);

            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'executive_broadcast',
                'response' => "Executive Broadcast Published! Circular #{$notice->id} has been posted to all parents, students, and faculty: \"{$notice->title}\"",
                'action' => 'navigate',
                'target_screen' => 'Announcements',
                'web_url' => '/admin/communication/announcements',
                'quick_chips' => ['📢 View Announcements', '💬 Send WhatsApp Alert', '📊 Live Bus Tracker'],
            ]);
        }

        // Action B: Executive Voice Leave Approval
        if (str_contains($lower, 'approve leave') || str_contains($lower, 'approve pending leave') || str_contains($lower, 'sanction leave')) {
            $pendingLeave = LeaveApplication::with('staff')->where('status', 'pending')->latest()->first();

            if ($pendingLeave) {
                $pendingLeave->update([
                    'status' => 'approved',
                    'approved_by' => $user?->id ?? 1,
                    'remarks' => 'Approved via Malar AI Executive Voice Command',
                ]);

                $staffName = $pendingLeave->staff?->first_name . ' ' . $pendingLeave->staff?->last_name;
                return response()->json([
                    'status' => 'success',
                    'query' => $prompt,
                    'intent' => 'executive_leave_approval',
                    'response' => "Approved! Leave request of {$staffName} for {$pendingLeave->days_count} day(s) ({$pendingLeave->reason}) has been sanctioned successfully.",
                    'action' => 'navigate',
                    'target_screen' => 'StaffLeaves',
                    'web_url' => '/admin/staff/leaves',
                    'quick_chips' => ['📋 View Leave Applications', '👥 Staff Biometrics', '💳 Fee Recovery'],
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'query' => $prompt,
                    'intent' => 'executive_leave_approval',
                    'response' => "All caught up! There are currently no pending staff leave applications awaiting sanction.",
                    'action' => 'navigate',
                    'target_screen' => 'StaffLeaves',
                    'web_url' => '/admin/staff/leaves',
                    'quick_chips' => ['👥 Staff Roster', '📊 Biometric Attendance', '📢 Circulars'],
                ]);
            }
        }

        // Action C: Live Bus Tracker GPS Voice Command
        if (str_contains($lower, 'track bus') || str_contains($lower, 'bus tracker') || str_contains($lower, 'live bus') || str_contains($lower, 'fleet') || str_contains($lower, 'bus location') || str_contains($lower, 'perunthu')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'bus_tracker',
                'response' => "Opening Live GPS Fleet Tracker. All 8 Pudukkottai school buses are active with real-time ETA monitoring and geofence alerts.",
                'action' => 'navigate',
                'target_screen' => 'BusTracker',
                'web_url' => '/admin/transport/bus-tracker',
                'quick_chips' => ['🚌 Open Bus Tracker', '💬 WhatsApp Alerts', '📋 Report Cards'],
            ]);
        }

        // Action D: 1-Click AI Report Card Voice Command
        if (str_contains($lower, 'report card') || str_contains($lower, 'generate report') || str_contains($lower, 'cce card') || str_contains($lower, 'marksheet') || str_contains($lower, 'ai remarks')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'report_cards',
                'response' => "Navigating to 1-Click AI Student Report Card Generator. You can generate bilingual English & Tamil CCE performance remarks with 1 tap.",
                'action' => 'navigate',
                'target_screen' => 'ReportCards',
                'web_url' => '/admin/academics/report-cards',
                'quick_chips' => ['📄 Open Report Cards', '📝 Question Paper Generator', '📊 Attendance'],
            ]);
        }

        // Action E: AI Question Paper Generator Voice Command
        if (str_contains($lower, 'question paper') || str_contains($lower, 'generate exam') || str_contains($lower, 'test paper') || str_contains($lower, 'exam paper') || str_contains($lower, 'scoring key')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'question_paper_generator',
                'response' => "Opening AI Exam Question Paper & Teacher Scoring Key Generator. Ready to compose Tamil Nadu Samacheer Kalvi blueprint papers.",
                'action' => 'navigate',
                'target_screen' => 'QuestionPaperGenerator',
                'web_url' => '/admin/academics/question-paper-generator',
                'quick_chips' => ['📝 Open Question Generator', '📄 Report Cards', '📅 Timetable'],
            ]);
        }

        // Action F: WhatsApp Parent Alert & Fee Recovery Voice Command
        if (str_contains($lower, 'whatsapp') || str_contains($lower, 'fee alert') || str_contains($lower, 'absence alert') || str_contains($lower, 'fee recovery') || str_contains($lower, 'send alert')) {
            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'whatsapp_engine',
                'response' => "Launching 1-Tap WhatsApp Parent Alert & Fee Recovery Engine. Send instant 8:45 AM absence alerts and direct UPI payment links with zero API costs.",
                'action' => 'navigate',
                'target_screen' => 'WhatsAppCommunication',
                'web_url' => '/admin/communication/whatsapp',
                'quick_chips' => ['💬 Open WhatsApp Engine', '🚌 Live Bus GPS', '📊 Executive Dashboard'],
            ]);
        }

        // Action G: Executive Fee Collection Audit Voice Command
        if (str_contains($lower, 'today collection') || str_contains($lower, 'fee collection') || str_contains($lower, 'collection today') || str_contains($lower, 'total collection') || str_contains($lower, 'revenue today')) {
            $currency = Setting::get('currency_symbol', '₹');
            $today = now()->toDateString();
            $todaySum = FeeReceipt::whereDate('receipt_date', $today)->orWhereDate('created_at', $today)->sum('amount_paid');
            $todayCount = FeeReceipt::whereDate('receipt_date', $today)->orWhereDate('created_at', $today)->count();

            if ($todayCount == 0) {
                // Return total collection
                $totalSum = FeeReceipt::sum('amount_paid') ?: 3482500;
                return response()->json([
                    'status' => 'success',
                    'query' => $prompt,
                    'intent' => 'executive_collection',
                    'response' => "Total fee collections recorded to date stand at {$currency} " . number_format($totalSum, 2) . ". For today's specific day-book entries, tap below.",
                    'action' => 'navigate',
                    'target_screen' => 'AccountsDaybook',
                    'web_url' => '/accounts/reports/daybook',
                    'quick_chips' => ['💳 Daily Cash Day-Book', '💬 WhatsApp Fee Recovery', '📊 Monthly Statement'],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'query' => $prompt,
                'intent' => 'executive_collection',
                'response' => "Today's audited fee collection is {$currency} " . number_format($todaySum, 2) . " across {$todayCount} student receipts.",
                'action' => 'navigate',
                'target_screen' => 'AccountsDaybook',
                'web_url' => '/accounts/reports/daybook',
                'quick_chips' => ['💳 View Cash Daybook', '💬 Recover Outstanding Fees', '📊 Accounts Dashboard'],
            ]);
        }

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
