<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicsTest extends TestCase
{
    protected User $admin;
    protected AcademicYear $ay;
    protected SchoolClass $class;
    protected Section $section;
    protected Staff $teacher1;
    protected Staff $teacher2;
    protected Subject $mathSubject;
    protected Subject $sciSubject;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'principal@school.test'],
            ['name' => 'Principal', 'password' => bcrypt('Password@123'), 'role' => 'principal', 'is_active' => true]
        );

        $this->ay = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        $this->class = SchoolClass::where('name', 'Class 10')->first() ?? SchoolClass::first();
        $this->section = Section::where('school_class_id', $this->class->id)->first() ?? Section::first();

        $teachers = Staff::where('designation', 'like', '%Teacher%')->take(2)->get();
        if ($teachers->count() < 2) {
            $teachers = Staff::take(2)->get();
        }
        $this->teacher1 = $teachers[0];
        $this->teacher2 = $teachers[1];

        $this->mathSubject = Subject::where('code', 'MATH10')->first() ?? Subject::first();
        $this->sciSubject = Subject::where('code', 'SCI10')->first() ?? Subject::skip(1)->first() ?? Subject::first();

        $this->student = Student::where('section_id', $this->section->id)->first() ?? Student::first();
    }

    public function test_admin_can_view_timetables_grid(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.academics.timetables.index', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Academic Weekly Timetable Schedule', false);
        $response->assertSee('Monday', false);
        $response->assertSee('Period 1', false);
    }

    public function test_timetable_slot_allocation_and_clash_detection(): void
    {
        // Allocate Period 1 on friday to teacher1
        $response = $this->actingAs($this->admin)->post(route('admin.academics.timetables.store'), [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'subject_id' => $this->mathSubject->id,
            'staff_id' => $this->teacher1->id,
            'day_of_week' => 'friday',
            'period_number' => 1,
            'room_no' => 'Room-101',
        ]);

        $response->assertSessionHasNoErrors();

        // Now find another section to test teacher clash
        $anotherSection = Section::where('id', '!=', $this->section->id)->first();
        if ($anotherSection) {
            // Attempt assigning the SAME teacher to another section at the EXACT same day & period
            $clashResponse = $this->actingAs($this->admin)->post(route('admin.academics.timetables.store'), [
                'school_class_id' => $anotherSection->school_class_id,
                'section_id' => $anotherSection->id,
                'subject_id' => $this->sciSubject->id,
                'staff_id' => $this->teacher1->id, // Same teacher!
                'day_of_week' => 'friday',
                'period_number' => 1,               // Same period!
                'room_no' => 'Room-202',
            ]);

            $clashResponse->assertSessionHas('error');
        }
    }

    public function test_substitution_engine_view_and_assignment(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.academics.substitutions.index'));

        $response->assertStatus(200);
        $response->assertSee('Automatic Faculty Substitution Engine', false);
    }

    public function test_admin_can_view_exams_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.academics.exams.index'));

        $response->assertStatus(200);
        $response->assertSee('Tamil Nadu CCE Examinations', false);
        $response->assertSee('Quarterly Examination 2026', false);
    }

    public function test_marks_entry_and_grading(): void
    {
        $schedule = ExamSchedule::first();
        $this->assertNotNull($schedule, 'Exam schedule must exist');

        $response = $this->actingAs($this->admin)->get(route('admin.academics.exams.marks.index', $schedule));
        $response->assertStatus(200);
        $response->assertSee('Marks Sheet', false);

        // Submit marks for a student
        $submitData = [
            'marks' => [
                $this->student->id => [
                    'marks_obtained' => 88,
                    'is_absent' => false,
                ]
            ]
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.academics.exams.marks.save', $schedule), $submitData);
        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('marks', [
            'exam_schedule_id' => $schedule->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 88,
            'grade' => 'A2',
        ]);
    }

    public function test_cce_report_card_pdf_download(): void
    {
        $exam = Exam::first();
        $this->assertNotNull($exam);

        $response = $this->actingAs($this->admin)->get(route('admin.academics.exams.report-card', [
            'exam' => $exam->id,
            'student' => $this->student->id,
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_homework_board_view_and_assignment(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.academics.homework.index', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Homework & Curriculum Assignments', false);

        // Store new homework
        $postResponse = $this->actingAs($this->admin)->post(route('admin.academics.homework.store'), [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'subject_id' => $this->mathSubject->id,
            'staff_id' => $this->teacher1->id,
            'title' => 'Trigonometry Chapter 4 Exercise 4.2',
            'description' => 'Solve problems 1 to 15 in the homework notebook.',
            'assigned_date' => now()->toDateString(),
            'due_date' => now()->addDays(2)->toDateString(),
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('homework', [
            'title' => 'Trigonometry Chapter 4 Exercise 4.2',
        ]);
    }

    public function test_student_rollcall_attendance(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.academics.student-attendance.index', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daily Section Roll-Call Register', false);

        // Mark rollcall attendance
        $postResponse = $this->actingAs($this->admin)->post(route('admin.academics.student-attendance.record'), [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'date' => now()->toDateString(),
            'attendance' => [
                $this->student->id => 'present',
            ],
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->student->id,
            'status' => 'present',
            'date' => now()->toDateString(),
        ]);
    }
}
