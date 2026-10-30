<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Payroll;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Compute full Indian salary structure.
     */
    public function calculateSalaryBreakdown(float $basicSalary): array
    {
        $da = round($basicSalary * 0.20, 2);      // Dearness Allowance 20%
        $hra = round($basicSalary * 0.10, 2);     // House Rent Allowance 10%
        $special = 2000.00;                       // Academic / Conveyance Allowance
        $gross = $basicSalary + $da + $hra + $special;

        $pf = round($basicSalary * 0.12, 2);      // Provident Fund 12%
        $tax = 200.00;                            // Tamil Nadu Professional Tax standard
        $deductions = $pf + $tax;

        $net = $gross - $deductions;

        return [
            'basic_salary' => $basicSalary,
            'da_amount' => $da,
            'hra_amount' => $hra,
            'special_allowance' => $special,
            'gross_salary' => $gross,
            'pf_deduction' => $pf,
            'tax_deduction' => $tax,
            'total_deductions' => $deductions,
            'net_salary' => $net,
        ];
    }

    /**
     * Generate or fetch monthly payroll sheet for all active staff.
     */
    public function generateMonthlyPayroll(string $month): array
    {
        $activeStaff = Staff::with('user')->where('status', 'active')->get();
        $generated = 0;

        DB::transaction(function () use ($activeStaff, $month, &$generated) {
            foreach ($activeStaff as $index => $st) {
                $existing = Payroll::where('staff_id', $st->id)->where('month', $month)->first();
                if ($existing) {
                    continue;
                }

                $breakdown = $this->calculateSalaryBreakdown((float) $st->basic_salary);

                // Count attendance in this month
                $carbonMonth = Carbon::createFromFormat('Y-m', $month);
                $start = $carbonMonth->copy()->startOfMonth();
                $end = $carbonMonth->copy()->endOfMonth();

                $presentDays = StaffAttendance::where('staff_id', $st->id)
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->whereIn('status', ['present', 'late'])
                    ->count() ?: 24;

                $leaveDays = StaffAttendance::where('staff_id', $st->id)
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->where('status', 'leave')
                    ->count() ?: 1;

                $count = Payroll::where('month', $month)->count() + 1;
                $payslipNo = "SCH/PAY/{$month}/" . str_pad($count, 4, '0', STR_PAD_LEFT);

                Payroll::create([
                    'staff_id' => $st->id,
                    'month' => $month,
                    'payslip_number' => $payslipNo,
                    ...$breakdown,
                    'working_days' => 26,
                    'present_days' => min(26, $presentDays),
                    'leave_days' => $leaveDays,
                    'status' => 'paid',
                    'payment_date' => Carbon::now()->toDateString(),
                    'payment_method' => 'Bank Transfer / NEFT',
                ]);

                $generated++;
            }

            if ($generated > 0) {
                AuditLog::record('monthly_payroll_generated', null, null, [
                    'month' => $month,
                    'count' => $generated,
                ]);
            }
        });

        $payrolls = Payroll::with(['staff.user'])
            ->where('month', $month)
            ->orderBy('id')
            ->get();

        $totalPayout = $payrolls->sum('net_salary');
        $totalGross = $payrolls->sum('gross_salary');
        $totalDeductions = $payrolls->sum('total_deductions');

        return [
            'payrolls' => $payrolls,
            'total_payout' => $totalPayout,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'month' => $month,
            'staff_count' => $payrolls->count(),
        ];
    }

    /**
     * Generate PDF payslip.
     */
    public function renderPayslipPdf(Payroll $payroll)
    {
        $payroll->load(['staff.user']);

        $pdf = Pdf::loadView('staff.payslip_pdf', compact('payroll'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }
}
