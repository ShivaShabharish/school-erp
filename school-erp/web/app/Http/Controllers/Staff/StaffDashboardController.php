<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $metrics = [
            'monthly_attendance_pct' => 96.0,
            'leave_balance' => 8,
            'today_classes_count' => 5,
            'pending_homework_reviews' => 2,
        ];

        return view('staff.dashboard', compact('user', 'metrics'));
    }
}
