<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\AdmissionEnquiry;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function enquiries(Request $request): View
    {
        $query = AdmissionEnquiry::with('schoolClass')->orderBy('id', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('enquiry_no', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('parent_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $enquiries = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        $counts = [
            'total' => AdmissionEnquiry::count(),
            'pending' => AdmissionEnquiry::where('status', 'pending')->count(),
            'reviewed' => AdmissionEnquiry::where('status', 'reviewed')->count(),
            'admitted' => AdmissionEnquiry::where('status', 'admitted')->count(),
        ];

        return view('students.enquiries', compact('enquiries', 'classes', 'counts'));
    }

    public function storeEnquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'parent_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $count = AdmissionEnquiry::count() + 1;
        $enquiryNo = 'ENQ-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $enquiry = AdmissionEnquiry::create([
            ...$validated,
            'enquiry_no' => $enquiryNo,
            'status' => 'pending',
        ]);

        AuditLog::record('enquiry_registered', $enquiry, null, [
            'enquiry_no' => $enquiry->enquiry_no,
            'student' => $enquiry->full_name,
        ]);

        return redirect()->route('admin.admissions.enquiries')
            ->with('success', "Prospective admission enquiry {$enquiryNo} registered successfully!");
    }

    public function convertEnquiry(AdmissionEnquiry $enquiry, Request $request): RedirectResponse
    {
        $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        $today = Carbon::today()->toDateString();

        DB::transaction(function () use ($enquiry, $request, $today) {
            $lastStudent = Student::withTrashed()->orderBy('id', 'desc')->first();
            $nextNum = $lastStudent ? ($lastStudent->id + 1) : 1;
            $admNo = 'ADM-2026-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $student = Student::create([
                'admission_no' => $admNo,
                'first_name' => $enquiry->first_name,
                'last_name' => $enquiry->last_name,
                'gender' => $enquiry->gender,
                'dob' => $enquiry->dob ?: '2014-06-01',
                'school_class_id' => $enquiry->school_class_id,
                'section_id' => $request->section_id,
                'roll_number' => str_pad(rand(1, 40), 2, '0', STR_PAD_LEFT),
                'admission_date' => $today,
                'status' => 'active',
                'father_name' => $enquiry->parent_name,
                'parent_phone' => $enquiry->phone,
                'parent_email' => $enquiry->email,
                'emergency_contact' => $enquiry->phone,
                'address' => $enquiry->address ?: 'Pudukkottai, Tamil Nadu',
                'city' => 'Pudukkottai',
                'state' => 'Tamil Nadu',
            ]);

            $enquiry->update(['status' => 'admitted']);

            AuditLog::record('enquiry_converted_to_admission', $student, null, [
                'enquiry_no' => $enquiry->enquiry_no,
                'admission_no' => $admNo,
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', "Enquiry {$enquiry->enquiry_no} successfully converted to official admission!");
    }
}
