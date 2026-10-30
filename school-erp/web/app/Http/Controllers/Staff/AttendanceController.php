<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        $staffMembers = Staff::with(['user', 'attendances' => function ($q) use ($date) {
            $q->where('date', $date);
        }])->where('status', 'active')->orderBy('id')->get();

        $summary = $this->attendanceService->getTodayAttendanceSummary();

        return view('staff.attendance', compact('staffMembers', 'summary', 'date'));
    }

    public function biometricSimulator(): View
    {
        $staffMembers = Staff::with('user')->where('status', 'active')->orderBy('id')->get();
        $devices = [
            ['id' => 'BIO-GATE-01', 'name' => 'Main Gate Turnstile (BIO-GATE-01)'],
            ['id' => 'BIO-STAFF-02', 'name' => 'Staff Room Terminal (BIO-STAFF-02)'],
            ['id' => 'BIO-ADMIN-03', 'name' => 'Admin Block Bio-Scanner (BIO-ADMIN-03)'],
        ];

        $todayPunches = StaffAttendance::with('staff.user')
            ->where('date', Carbon::today()->toDateString())
            ->whereNotNull('punch_in_time')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('staff.simulator', compact('staffMembers', 'devices', 'todayPunches'));
    }

    public function simulatePunch(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'device_id' => ['required', 'string'],
            'timestamp' => ['nullable', 'date'],
        ]);

        $result = $this->attendanceService->recordBiometricPunch(
            deviceId: $validated['device_id'],
            staffId: $validated['staff_id'],
            timestamp: $validated['timestamp'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.staff.biometric-simulator')
            ->with('success', $result['message']);
    }
}
