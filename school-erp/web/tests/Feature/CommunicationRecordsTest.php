<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\CalendarEvent;
use App\Models\Certificate;
use App\Models\ParentNote;
use App\Models\PtmSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class CommunicationRecordsTest extends TestCase
{
    protected User $admin;
    protected User $principal;
    protected User $parent;
    protected AcademicYear $academicYear;
    protected SchoolClass $class;
    protected Section $section;
    protected Student $student;
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            ['name' => 'System Administrator', 'password' => bcrypt('Password@123'), 'role' => 'admin', 'is_active' => true]
        );

        $this->principal = User::firstOrCreate(
            ['email' => 'principal@school.test'],
            ['name' => 'School Principal', 'password' => bcrypt('Password@123'), 'role' => 'principal', 'is_active' => true]
        );

        $this->parent = User::firstOrCreate(
            ['email' => 'parent@school.test'],
            ['name' => 'School Parent', 'password' => bcrypt('Password@123'), 'role' => 'parent', 'is_active' => true]
        );

        $this->academicYear = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        $this->class = SchoolClass::first();
        $this->section = $this->class->sections()->first() ?? Section::first();
        $this->student = Student::first();
        $this->staff = Staff::first();
    }

    public function test_admin_can_view_calendar_and_create_event(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.communication.calendar.index'));
        $response->assertStatus(200);
        $response->assertSee('School Events & Academic Calendar', false);

        $uniqueTitle = 'Pongal Cultural Event ' . uniqid();
        $payload = [
            'academic_year_id' => $this->academicYear->id,
            'title' => $uniqueTitle,
            'event_type' => 'function',
            'start_date' => '2027-01-13',
            'end_date' => '2027-01-13',
            'description' => 'Pongal pot making and traditional games in school campus.',
            'target_audience' => 'all',
            'is_holiday' => 0,
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.communication.calendar.store'), $payload);
        $postResponse->assertRedirect(route('admin.communication.calendar.index', ['month' => 1, 'year' => 2027]));

        $this->assertDatabaseHas('calendar_events', [
            'title' => $uniqueTitle,
            'academic_year_id' => $this->academicYear->id,
        ]);
    }

    public function test_admin_can_view_announcements_and_broadcast_circular(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.communication.announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Official Circulars', false);

        $uniqueTitle = 'Health Camp Notice ' . uniqid();
        $payload = [
            'academic_year_id' => $this->academicYear->id,
            'title' => $uniqueTitle,
            'content' => 'Qualified doctors from District Hospital Pudukkottai will conduct free comprehensive vision and dental checks.',
            'category' => 'general',
            'target_type' => 'all',
            'publish_date' => now()->toDateString(),
            'is_pinned' => 1,
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.communication.announcements.store'), $payload);
        $postResponse->assertRedirect(route('admin.communication.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => $uniqueTitle,
            'is_pinned' => 1,
        ]);
    }

    public function test_admin_can_schedule_ptm_for_class_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.communication.ptm.index'));
        $response->assertStatus(200);
        $response->assertSee('Parent-Teacher Meetings');

        $uniqueVenue = 'Main Auditorium Room ' . rand(100, 999);
        $payload = [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'meeting_date' => now()->addDays(7)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:30',
            'venue' => $uniqueVenue,
            'agenda' => 'Quarterly progress card review and parent consultation.',
            'status' => 'scheduled',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.communication.ptm.store'), $payload);
        $postResponse->assertRedirect(route('admin.communication.ptm.index'));

        $this->assertDatabaseHas('ptm_schedules', [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'venue' => $uniqueVenue,
        ]);
    }

    public function test_parent_note_submission_and_reply(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.communication.notes.index'));
        $response->assertStatus(200);
        $response->assertSee('Teacher Notes Inbox', false);

        $uniqueSubject = 'Leave query ' . uniqid();
        $note = ParentNote::create([
            'student_id' => $this->student->id,
            'parent_id' => $this->parent->id,
            'subject' => $uniqueSubject,
            'message' => 'My child is suffering from viral fever and advised 2 days rest by pediatrician.',
            'status' => 'pending',
        ]);

        $replyResponse = $this->actingAs($this->admin)->post(route('admin.communication.notes.reply', $note), [
            'reply' => 'Noted and leave approved. Please submit medical certificate upon resumption.',
            'status' => 'replied',
        ]);

        $replyResponse->assertRedirect(route('admin.communication.notes.index'));

        $this->assertDatabaseHas('parent_notes', [
            'id' => $note->id,
            'status' => 'replied',
            'reply' => 'Noted and leave approved. Please submit medical certificate upon resumption.',
        ]);
    }

    public function test_admin_can_issue_bonafide_certificate_and_download_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.communication.certificates.index'));
        $response->assertStatus(200);
        $response->assertSee('Official Certificates');

        $payload = [
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'certificate_type' => 'bonafide',
            'issue_date' => now()->toDateString(),
            'reason' => 'Bank Account Opening',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.communication.certificates.generate'), $payload);
        $postResponse->assertRedirect(route('admin.communication.certificates.index'));

        $cert = Certificate::where('student_id', $this->student->id)
            ->where('certificate_type', 'bonafide')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($cert);
        $this->assertStringContainsString('BON', $cert->certificate_no);

        // Test PDF download endpoint
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.communication.certificates.download', $cert));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_issue_transfer_certificate_and_download_pdf(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'certificate_type' => 'transfer_certificate',
            'issue_date' => now()->toDateString(),
            'leaving_date' => now()->toDateString(),
            'conduct' => 'EXCELLENT',
            'fee_dues_cleared' => 1,
            'reason_for_leaving' => 'Course Completed',
            'identification_marks' => '1. Mole on left elbow; 2. Scar on chin',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.communication.certificates.generate'), $payload);
        $postResponse->assertRedirect(route('admin.communication.certificates.index'));

        $cert = Certificate::where('student_id', $this->student->id)
            ->where('certificate_type', 'transfer_certificate')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($cert);
        $this->assertStringContainsString('TC', $cert->certificate_no);

        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.communication.certificates.download', $cert));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_id_cards_and_print_sheets(): void
    {
        // 1. Index Preview
        $response = $this->actingAs($this->admin)->get(route('admin.communication.id-cards.index', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Identity Cards', false);

        // 2. Print Students Sheet
        $printStudentsResponse = $this->actingAs($this->admin)->get(route('admin.communication.id-cards.print-students', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
        ]));
        $printStudentsResponse->assertStatus(200);
        $printStudentsResponse->assertSee('Student Identity Cards Print Sheet', false);

        // 3. Print Staff Sheet
        $printStaffResponse = $this->actingAs($this->admin)->get(route('admin.communication.id-cards.print-staff'));
        $printStaffResponse->assertStatus(200);
        $printStaffResponse->assertSee('Staff Identity Cards Print Sheet', false);
    }
}
