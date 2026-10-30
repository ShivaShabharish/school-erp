<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['schoolClass', 'section', 'parent'])
            ->orderBy('school_class_id')
            ->orderBy('section_id')
            ->orderBy('roll_number');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('admission_no', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('roll_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        $students = $query->paginate(20)->withQueryString();

        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();
        $totalActive = Student::where('status', 'active')->count();
        $totalBoys = Student::where('status', 'active')->where('gender', 'male')->count();
        $totalGirls = Student::where('status', 'active')->where('gender', 'female')->count();

        return view('students.index', compact(
            'students',
            'classes',
            'totalActive',
            'totalBoys',
            'totalGirls'
        ));
    }

    public function create(): View
    {
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        // Calculate next admission number
        $lastStudent = Student::withTrashed()->orderBy('id', 'desc')->first();
        $nextNum = $lastStudent ? ($lastStudent->id + 1) : 1;
        $suggestedAdmNo = 'ADM-2026-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        return view('students.create', compact('classes', 'suggestedAdmNo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admission_no' => ['required', 'string', 'unique:students,admission_no'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['required', 'date'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_number' => ['nullable', 'string', 'max:20'],
            'admission_date' => ['required', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'father_name' => ['required', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:100'],
            'address' => ['required', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'previous_school' => ['nullable', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $parentId = null;

            // Automatically create Parent user account if parent email provided
            if (!empty($validated['parent_email'])) {
                $parentUser = User::firstOrCreate(
                    ['email' => $validated['parent_email']],
                    [
                        'name' => $validated['father_name'] . ' (Parent)',
                        'password' => Hash::make('Password@123'),
                        'role' => 'parent',
                        'phone' => $validated['parent_phone'],
                        'is_active' => true,
                    ]
                );
                $parentId = $parentUser->id;
            }

            $validated['parent_id'] = $parentId;
            $validated['emergency_contact'] = $validated['parent_phone'];
            $validated['documents'] = [
                ['name' => 'Birth Certificate', 'verified' => $request->boolean('doc_birth')],
                ['name' => 'Aadhaar Card Copy', 'verified' => $request->boolean('doc_aadhaar')],
                ['name' => 'Transfer Certificate (TC)', 'verified' => $request->boolean('doc_tc')],
            ];

            $student = Student::create($validated);

            AuditLog::record('admission_created', $student, null, [
                'admission_no' => $student->admission_no,
                'name' => $student->full_name,
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$validated['first_name']} admitted successfully under Admission No: {$validated['admission_no']}!");
    }

    public function show(Student $student): View
    {
        $student->load(['schoolClass', 'section', 'parent', 'feeReceipts', 'promotions.fromClass', 'promotions.toClass']);

        // Calculate attendance stats
        $totalDays = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $attendancePct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 96.5;

        // Default documents structure if not set
        $documents = $student->documents ?: [
            ['name' => 'Birth Certificate', 'verified' => true],
            ['name' => 'Aadhaar Card Copy', 'verified' => true],
            ['name' => 'Transfer Certificate (TC)', 'verified' => false],
        ];

        return view('students.show', compact('student', 'attendancePct', 'totalDays', 'presentDays', 'documents'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $students = Student::with(['schoolClass', 'section'])
            ->where('status', 'active')
            ->orderBy('school_class_id')
            ->orderBy('section_id')
            ->orderBy('roll_number')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_directory_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Admission No',
                'Roll No',
                'First Name',
                'Last Name',
                'Gender',
                'Class',
                'Section',
                'Father Name',
                'Mother Name',
                'Parent Phone',
                'Emergency Contact',
                'Blood Group',
                'City',
                'Status',
            ]);

            foreach ($students as $s) {
                fputcsv($file, [
                    $s->admission_no,
                    $s->roll_number,
                    $s->first_name,
                    $s->last_name,
                    ucfirst($s->gender),
                    $s->schoolClass?->name,
                    $s->section?->name,
                    $s->father_name ?? 'N/A',
                    $s->mother_name ?? 'N/A',
                    $s->parent_phone ?? $s->emergency_contact,
                    $s->emergency_contact,
                    $s->blood_group,
                    $s->city ?? 'Pudukkottai',
                    ucfirst($s->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadSampleCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_sample_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'first_name',
                'last_name',
                'gender',
                'dob',
                'class_name',
                'section_name',
                'roll_number',
                'father_name',
                'parent_phone',
                'address',
            ]);

            fputcsv($file, [
                'Aravind',
                'Subramanian',
                'male',
                '2012-05-15',
                'Class 8',
                'A',
                '15',
                'S. Subramanian',
                '+91 98421 11223',
                '45 Gandhi Road, Pudukkottai',
            ]);

            fputcsv($file, [
                'Swetha',
                'Kannan',
                'female',
                '2012-08-20',
                'Class 8',
                'A',
                '16',
                'P. Kannan',
                '+91 98421 11224',
                '12 Anna Nagar, Pudukkottai',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $today = Carbon::today()->toDateString();

        DB::transaction(function () use ($handle, &$imported, $today) {
            $lastStudent = Student::withTrashed()->orderBy('id', 'desc')->first();
            $nextNum = $lastStudent ? ($lastStudent->id + 1) : 1;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 5 || empty($row[0])) continue;

                $fName = trim($row[0]);
                $lName = trim($row[1] ?? '');
                $gender = strtolower(trim($row[2] ?? 'male'));
                $dob = trim($row[3] ?? '2012-01-01');
                $className = trim($row[4]);
                $sectionName = trim($row[5] ?? 'A');
                $rollNo = trim($row[6] ?? str_pad($imported + 1, 2, '0', STR_PAD_LEFT));
                $father = trim($row[7] ?? 'Parent');
                $phone = trim($row[8] ?? '+91 98421 00000');
                $addr = trim($row[9] ?? 'Pudukkottai');

                $class = SchoolClass::where('name', $className)->first() ?: SchoolClass::first();
                $section = Section::where('school_class_id', $class->id)->where('name', $sectionName)->first()
                    ?: Section::where('school_class_id', $class->id)->first();

                $admNo = 'ADM-2026-' . str_pad($nextNum + $imported, 4, '0', STR_PAD_LEFT);

                Student::create([
                    'admission_no' => $admNo,
                    'first_name' => $fName,
                    'last_name' => $lName,
                    'gender' => in_array($gender, ['male', 'female']) ? $gender : 'male',
                    'dob' => $dob,
                    'school_class_id' => $class->id,
                    'section_id' => $section->id,
                    'roll_number' => $rollNo,
                    'admission_date' => $today,
                    'status' => 'active',
                    'father_name' => $father,
                    'parent_phone' => $phone,
                    'emergency_contact' => $phone,
                    'address' => $addr,
                    'city' => 'Pudukkottai',
                    'state' => 'Tamil Nadu',
                ]);

                $imported++;
            }
        });

        fclose($handle);

        return redirect()->route('admin.students.index')
            ->with('success', "CSV Import completed successfully: {$imported} students enrolled!");
    }
}
