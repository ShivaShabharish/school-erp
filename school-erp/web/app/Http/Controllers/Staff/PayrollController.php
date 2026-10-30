<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index(Request $request): View
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $payrollData = $this->payrollService->generateMonthlyPayroll($month);

        return view('staff.payroll', [
            'payrolls' => $payrollData['payrolls'],
            'totalPayout' => $payrollData['total_payout'],
            'totalGross' => $payrollData['total_gross'],
            'totalDeductions' => $payrollData['total_deductions'],
            'month' => $month,
            'staffCount' => $payrollData['staff_count'],
        ]);
    }

    public function downloadPayslip(Payroll $payroll): Response
    {
        $pdf = $this->payrollService->renderPayslipPdf($payroll);

        $filename = "Payslip-{$payroll->staff->employee_code}-{$payroll->month}.pdf";

        return $pdf->download($filename);
    }
}
