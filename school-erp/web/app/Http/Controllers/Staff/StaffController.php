<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Staff;
use App\Models\User;
use App\Services\LeaveService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService,
        protected PayrollService $payrollService
    ) {}

    public function index(Request $request): View
    {
        $query = Staff::with(['user', 'attendances' => function ($q) {
            $q->where('date', Carbon::today()->toDateString());
        }])->orderBy('id');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('employee_code', 'like', "%{$s}%")
                    ->orWhere('designation', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        $staffMembers = $query->paginate(15)->withQueryString();

        $departments = Staff::select('department')->distinct()->pluck('department');
        $totalActive = Staff::where('status', 'active')->count();
        $totalTeaching = Staff::where('status', 'active')->where('department', 'Teaching')->count();
        $totalNonTeaching = $totalActive - $totalTeaching;

        return view('staff.index', compact(
            'staffMembers',
            'departments',
            'totalActive',
            'totalTeaching',
            'totalNonTeaching'
        ));
    }

    public function create(): View
    {
        $lastStaff = Staff::orderBy('id', 'desc')->first();
        $nextNum = $lastStaff ? ($lastStaff->id + 1) : 1;
        $suggestedCode = 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('staff.create', compact('suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'employee_code' => ['required', 'string', 'unique:staff,employee_code'],
            'designation' => ['required', 'string', 'max:100'],
            'department' => ['required', 'string', 'max:100'],
            'qualification' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'joining_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:5000'],
            'bank_account_no' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_ifsc' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('Password@123'),
                'role' => $validated['department'] === 'Teaching' ? 'teacher' : 'staff',
                'phone' => $validated['phone'],
                'is_active' => true,
            ]);

            $staff = Staff::create([
                'user_id' => $user->id,
                'employee_code' => $validated['employee_code'],
                'designation' => $validated['designation'],
                'department' => $validated['department'],
                'qualification' => $validated['qualification'] ?? 'Graduate',
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'joining_date' => $validated['joining_date'],
                'basic_salary' => $validated['basic_salary'],
                'bank_account_no' => $validated['bank_account_no'],
                'bank_name' => $validated['bank_name'],
                'bank_ifsc' => $validated['bank_ifsc'],
                'address' => $validated['address'],
                'status' => 'active',
            ]);

            // Initialize leave quotas for current academic year
            $currentYear = AcademicYear::current();
            if ($currentYear) {
                $this->leaveService->initializeQuotas($staff, $currentYear);
            }

            AuditLog::record('staff_onboarded', $staff, null, [
                'employee_code' => $staff->employee_code,
                'name' => $user->name,
                'designation' => $staff->designation,
            ]);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member {$validated['name']} registered successfully with Code: {$validated['employee_code']}!");
    }

    public function show(Staff $staff): View
    {
        $staff->load([
            'user',
            'attendances' => function ($q) {
                $q->latest('date')->limit(30);
            },
            'leaveQuotas.leaveType',
            'leaveApplications.leaveType',
            'payrolls' => function ($q) {
                $q->latest('month')->limit(12);
            },
        ]);

        $totalPunched = $staff->attendances()->whereIn('status', ['present', 'late'])->count();
        $onTimePunched = $staff->attendances()->where('status', 'present')->count();
        $punctualityRate = $totalPunched > 0 ? round(($onTimePunched / $totalPunched) * 100, 1) : 98.0;

        $salaryBreakdown = $this->payrollService->calculateSalaryBreakdown((float) $staff->basic_salary);

        return view('staff.show', compact('staff', 'punctualityRate', 'salaryBreakdown'));
    }
}
