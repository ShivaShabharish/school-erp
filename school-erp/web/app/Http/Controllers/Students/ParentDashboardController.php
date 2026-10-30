<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Sample ward data for foundation
        $ward = [
            'name' => 'Aarav Santhosh',
            'admission_no' => 'ADM-2026-0042',
            'class_section' => 'Class 8 - Section A',
            'roll_no' => '14',
            'attendance_pct' => 96.8,
            'fee_status' => 'Paid (Term 1 & Term 2)',
            'pending_fee' => 0,
            'recent_exam_grade' => 'A1 (92.4%)',
        ];

        return view('students.parent_dashboard', compact('user', 'ward'));
    }
}
