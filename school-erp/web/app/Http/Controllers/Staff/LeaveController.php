<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Services\LeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = LeaveApplication::with(['staff.user', 'leaveType', 'approver'])->latest('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->paginate(15)->withQueryString();

        $leaveTypes = LeaveType::all();
        $staffMembers = Staff::with('user')->where('status', 'active')->orderBy('id')->get();

        $counts = [
            'total' => LeaveApplication::count(),
            'pending' => LeaveApplication::where('status', 'pending')->count(),
            'approved' => LeaveApplication::where('status', 'approved')->count(),
            'rejected' => LeaveApplication::where('status', 'rejected')->count(),
        ];

        return view('staff.leaves', compact('applications', 'leaveTypes', 'staffMembers', 'counts', 'status'));
    }

    public function apply(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $staff = Staff::findOrFail($validated['staff_id']);
        $this->leaveService->applyLeave($staff, $validated);

        return redirect()->route('admin.staff.leaves.index')
            ->with('success', 'Leave application submitted successfully for review.');
    }

    public function approve(LeaveApplication $leaveApplication): RedirectResponse
    {
        $this->leaveService->approveLeave($leaveApplication, auth()->user());

        return redirect()->route('admin.staff.leaves.index')
            ->with('success', "Leave application for {$leaveApplication->staff->full_name} approved.");
    }

    public function reject(LeaveApplication $leaveApplication, Request $request): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $this->leaveService->rejectLeave($leaveApplication, auth()->user(), $request->input('remarks'));

        return redirect()->route('admin.staff.leaves.index')
            ->with('success', "Leave application for {$leaveApplication->staff->full_name} rejected.");
    }
}
