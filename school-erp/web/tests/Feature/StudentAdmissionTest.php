<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmissionEnquiry;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAdmissionTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role', 'principal')->first() ?? User::factory()->create([
            'role' => 'principal',
            'email' => 'principal@school.test',
        ]);
    }

    public function test_admin_can_view_student_directory(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.students.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Enrollment & Directory', false);
        $response->assertSee('Total Enrolled');
    }

    public function test_admin_can_view_new_admission_form(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.students.create'));

        $response->assertStatus(200);
        $response->assertSee('Student Admission Form');
        $response->assertSee('Suggested No:');
    }

    public function test_admin_can_register_new_student_admission(): void
    {
        $admin = $this->getAdminUser();
        $class = SchoolClass::first();
        $section = Section::where('school_class_id', $class->id)->first();

        $uniqueAdmNo = 'ADM-TEST-' . rand(10000, 99999);

        $payload = [
            'admission_no' => $uniqueAdmNo,
            'first_name' => 'Aditi',
            'last_name' => 'Ramanathan',
            'gender' => 'female',
            'dob' => '2015-08-20',
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll_number' => '99',
            'admission_date' => '2026-06-01',
            'blood_group' => 'O+',
            'father_name' => 'Ramanathan V.',
            'mother_name' => 'Kalyani R.',
            'parent_phone' => '9840199999',
            'parent_email' => 'ramanathan.' . rand(1000, 9999) . '@test.com',
            'address' => '45 Alangudi Road, Pudukkottai',
            'city' => 'Pudukkottai',
            'pincode' => '622001',
            'previous_school' => 'Vidya Mandir School',
            'doc_birth' => '1',
            'doc_aadhaar' => '1',
            'doc_tc' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.students.store'), $payload);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'admission_no' => $uniqueAdmNo,
            'first_name' => 'Aditi',
            'father_name' => 'Ramanathan V.',
        ]);
        $this->assertDatabaseHas('users', [
            'phone' => '9840199999',
            'role' => 'parent',
        ]);
    }

    public function test_admin_can_view_360_student_profile(): void
    {
        $admin = $this->getAdminUser();
        $student = Student::first();

        $response = $this->actingAs($admin)->get(route('admin.students.show', $student));

        $response->assertStatus(200);
        $response->assertSee($student->full_name);
        $response->assertSee($student->admission_no);
        $response->assertSee('Student Demographics');
        $response->assertSee('Parent / Guardian Details');
    }

    public function test_admin_can_export_students_csv(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.students.export-csv'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_manage_prospective_enquiries_and_convert(): void
    {
        $admin = $this->getAdminUser();
        $class = SchoolClass::first();
        $section = Section::where('school_class_id', $class->id)->first();

        // 1. Visit enquiries page
        $res = $this->actingAs($admin)->get(route('admin.admissions.enquiries'));
        $res->assertStatus(200);
        $res->assertSee('Prospective Admission Pipeline');

        // 2. Register enquiry
        $storeRes = $this->actingAs($admin)->post(route('admin.admissions.enquiry.store'), [
            'first_name' => 'Manoj',
            'last_name' => 'Kumar',
            'gender' => 'male',
            'dob' => '2016-04-10',
            'school_class_id' => $class->id,
            'parent_name' => 'Kumaravel P.',
            'phone' => '9443198765',
            'email' => 'kumaravel@test.com',
            'address' => 'Anna Nagar, Pudukkottai',
            'notes' => 'Seeking admission for next term',
        ]);

        $storeRes->assertRedirect(route('admin.admissions.enquiries'));
        $enquiry = AdmissionEnquiry::where('first_name', 'Manoj')->latest()->first();
        $this->assertNotNull($enquiry);
        $this->assertEquals('pending', $enquiry->status);

        // 3. Convert enquiry to student
        $convertRes = $this->actingAs($admin)->post(route('admin.admissions.enquiry.convert', $enquiry), [
            'section_id' => $section->id,
        ]);

        $convertRes->assertRedirect(route('admin.students.index'));
        $enquiry->refresh();
        $this->assertEquals('admitted', $enquiry->status);
        $this->assertDatabaseHas('students', [
            'first_name' => 'Manoj',
            'last_name' => 'Kumar',
            'school_class_id' => $class->id,
            'section_id' => $section->id,
        ]);
    }

    public function test_admin_can_execute_batch_promotion(): void
    {
        $admin = $this->getAdminUser();
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();
        $fromClass = $classes[0];
        $fromSection = $fromClass->sections[0];
        $toClass = $classes[1];
        $toSection = $toClass->sections[0];
        $academicYear = AcademicYear::first();

        // Fetch students in fromSection
        $students = Student::where('school_class_id', $fromClass->id)
            ->where('section_id', $fromSection->id)
            ->limit(2)
            ->get();

        $this->assertNotEmpty($students);

        $payload = [
            'from_class_id' => $fromClass->id,
            'from_section_id' => $fromSection->id,
            'to_class_id' => $toClass->id,
            'to_section_id' => $toSection->id,
            'academic_year_id' => $academicYear->id,
            'student_ids' => $students->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($admin)->post(route('admin.students.promotion.promote'), $payload);

        $response->assertRedirect(route('admin.students.promotion'));

        foreach ($students as $st) {
            $st->refresh();
            $this->assertEquals($toClass->id, $st->school_class_id);
            $this->assertEquals($toSection->id, $st->section_id);
            $this->assertDatabaseHas('student_promotions', [
                'student_id' => $st->id,
                'from_class_id' => $fromClass->id,
                'to_class_id' => $toClass->id,
            ]);
        }
    }

    public function test_classes_overview_and_section_roster(): void
    {
        $admin = $this->getAdminUser();
        $class = SchoolClass::with('sections')->first();
        $section = $class->sections->first();

        $overviewRes = $this->actingAs($admin)->get(route('admin.classes.index'));
        $overviewRes->assertStatus(200);
        $overviewRes->assertSee('Academic Classes & Section Roster', false);
        $overviewRes->assertSee($class->name);

        $rosterRes = $this->actingAs($admin)->get(route('admin.classes.show', [$class, $section]));
        $rosterRes->assertStatus(200);
        $rosterRes->assertSee($class->name);
        $rosterRes->assertSee('Section ' . $section->name);
        $rosterRes->assertSee("Today's Attendance", false);
    }
}
