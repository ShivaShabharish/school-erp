<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\SchoolClass;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $type = $request->get('type', 'all');

        $query = Certificate::with(['student.schoolClass', 'student.section', 'issuer'])
            ->where('academic_year_id', $currentYear?->id);

        if ($type !== 'all') {
            $query->where('certificate_type', $type);
        }

        $certificates = $query->orderByDesc('id')->paginate(12)->withQueryString();
        $classes = SchoolClass::with(['students' => function ($q) {
            $q->orderBy('first_name');
        }])->orderBy('order_sequence')->get();

        $bonafideCount = Certificate::where('academic_year_id', $currentYear?->id)
            ->where('certificate_type', 'bonafide')
            ->count();
        $tcCount = Certificate::where('academic_year_id', $currentYear?->id)
            ->where('certificate_type', 'transfer_certificate')
            ->count();

        return view('communication.certificates.index', compact(
            'certificates',
            'classes',
            'currentYear',
            'type',
            'bonafideCount',
            'tcCount'
        ));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'certificate_type' => ['required', 'in:bonafide,transfer_certificate,conduct'],
            'issue_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            // Specific TC fields
            'leaving_date' => ['nullable', 'date'],
            'conduct' => ['nullable', 'string', 'max:50'],
            'fee_dues_cleared' => ['nullable', 'boolean'],
            'reason_for_leaving' => ['nullable', 'string', 'max:200'],
            'identification_marks' => ['nullable', 'string', 'max:255'],
        ]);

        $ay = AcademicYear::find($validated['academic_year_id']);
        $yearSlug = $ay ? str_replace('20', '', explode('-', $ay->name)[0]) . '-' . str_replace('20', '', explode('-', $ay->name)[1] ?? '27') : '26-27';
        $prefix = match ($validated['certificate_type']) {
            'bonafide' => 'BON',
            'transfer_certificate' => 'TC',
            default => 'CERT',
        };

        $lastCert = Certificate::where('certificate_no', 'like', "SCH/{$yearSlug}/{$prefix}-%")
            ->orderByDesc('id')
            ->first();
        $nextSeq = 1;
        if ($lastCert && preg_match("/{$prefix}-(\d+)$/", $lastCert->certificate_no, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        $certificateNo = sprintf("SCH/%s/%s-%05d", $yearSlug, $prefix, $nextSeq);

        $details = [];
        if ($validated['certificate_type'] === 'transfer_certificate') {
            $details = [
                'leaving_date' => $validated['leaving_date'] ?? now()->toDateString(),
                'conduct' => $validated['conduct'] ?? 'Good',
                'fee_dues_cleared' => !empty($validated['fee_dues_cleared']) ? 'Yes' : 'No',
                'reason_for_leaving' => $validated['reason_for_leaving'] ?? 'Course Completed / Relocation',
                'identification_marks' => $validated['identification_marks'] ?? 'A mole on the right cheek',
            ];
        }

        $certificate = Certificate::create([
            'certificate_no' => $certificateNo,
            'student_id' => $validated['student_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'certificate_type' => $validated['certificate_type'],
            'issue_date' => $validated['issue_date'],
            'reason' => $validated['reason'] ?? ($validated['certificate_type'] === 'bonafide' ? 'Higher Education / Passport Application' : 'Transfer to Another School'),
            'details' => $details,
            'issued_by' => auth()->id(),
        ]);

        AuditLog::record('certificate_issued', $certificate, null, [
            'certificate_no' => $certificate->certificate_no,
            'type' => $certificate->certificate_type,
            'student_id' => $certificate->student_id,
        ]);

        return redirect()->route('admin.communication.certificates.index')
            ->with('success', "Certificate {$certificate->certificate_no} issued successfully.")
            ->with('cert_id', $certificate->id);
    }

    public function download(Certificate $certificate)
    {
        $certificate->load(['student.schoolClass', 'student.section', 'academicYear', 'issuer']);

        $viewName = $certificate->certificate_type === 'transfer_certificate'
            ? 'communication.certificates.tc_pdf'
            : 'communication.certificates.bonafide_pdf';

        $safeName = str_replace(['/', '\\'], '-', $certificate->certificate_no);
        $filename = "{$safeName}.pdf";

        $pdf = Pdf::loadView($viewName, compact('certificate'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf->download($filename);
    }
}
