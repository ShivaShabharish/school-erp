<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $payroll->payslip_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .title-badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            font-weight: bold;
            width: 20%;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
            width: 30%;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .salary-table th {
            background-color: #047857;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 8px;
            text-transform: uppercase;
            text-align: left;
        }
        .salary-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 10px;
        }
        .amt {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .net-box {
            background-color: #f0fdf4;
            border: 1.5px solid #059669;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        .net-title {
            font-size: 10px;
            color: #065f46;
            font-weight: bold;
            text-transform: uppercase;
        }
        .net-amt {
            font-size: 16px;
            font-weight: bold;
            color: #047857;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 2px;
        }
        .sign-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .sign-table td {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
            padding-top: 40px;
        }
        .sign-line {
            border-top: 1px dashed #94a3b8;
            width: 70%;
            margin: 0 auto;
            padding-top: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="school-name">Test Subject Higher Secondary School</div>
        <div class="school-sub">Affiliated to Tamil Nadu State Board of Education &bull; School Code: TN-PDK-0428</div>
        <div class="school-sub">Main Campus, Collectorate Road, Pudukkottai - 622001, Tamil Nadu</div>
        <div class="title-badge">SALARY PAYSLIP &bull; {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}</div>
    </div>

    <!-- Meta Information -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Payslip No:</td>
            <td class="meta-val">{{ $payroll->payslip_number }}</td>
            <td class="meta-label">Disbursal Date:</td>
            <td class="meta-val">{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('d-m-Y') : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Employee Code:</td>
            <td class="meta-val">{{ $payroll->staff->employee_code }}</td>
            <td class="meta-label">Working / Present:</td>
            <td class="meta-val">{{ $payroll->working_days }} Days / {{ $payroll->present_days }} Days</td>
        </tr>
        <tr>
            <td class="meta-label">Faculty Name:</td>
            <td class="meta-val">{{ $payroll->staff->user?->name }}</td>
            <td class="meta-label">Designation:</td>
            <td class="meta-val">{{ $payroll->staff->designation }}</td>
        </tr>
        <tr>
            <td class="meta-label">Department:</td>
            <td class="meta-val">{{ $payroll->staff->department }}</td>
            <td class="meta-label">Payment Mode:</td>
            <td class="meta-val">{{ $payroll->payment_method }}</td>
        </tr>
        <tr>
            <td class="meta-label">Bank Name:</td>
            <td class="meta-val">{{ $payroll->staff->bank_name ?: 'State Bank of India' }}</td>
            <td class="meta-label">A/C Number:</td>
            <td class="meta-val">{{ $payroll->staff->bank_account_no ?: '38491028471' }}</td>
        </tr>
    </table>

    <!-- Earnings & Deductions Columns -->
    <table class="salary-table">
        <thead>
            <tr>
                <th style="width: 32%;">Earnings Component</th>
                <th style="width: 18%;" class="amt">Amount (INR)</th>
                <th style="width: 32%;">Deductions Component</th>
                <th style="width: 18%;" class="amt">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Pay</td>
                <td class="amt">Rs. {{ number_format($payroll->basic_salary, 2) }}</td>
                <td>Provident Fund (PF 12%)</td>
                <td class="amt">Rs. {{ number_format($payroll->pf_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>Dearness Allowance (DA 20%)</td>
                <td class="amt">Rs. {{ number_format($payroll->da_amount, 2) }}</td>
                <td>Professional Tax (PT)</td>
                <td class="amt">Rs. {{ number_format($payroll->tax_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>House Rent Allowance (HRA 10%)</td>
                <td class="amt">Rs. {{ number_format($payroll->hra_amount, 2) }}</td>
                <td>Income Tax (TDS)</td>
                <td class="amt">Rs. 0.00</td>
            </tr>
            <tr>
                <td>Conveyance / Academic Allowance</td>
                <td class="amt">Rs. {{ number_format($payroll->special_allowance, 2) }}</td>
                <td>Loss of Pay (LOP)</td>
                <td class="amt">Rs. 0.00</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td>Total Gross Earnings</td>
                <td class="amt" style="color: #047857;">Rs. {{ number_format($payroll->gross_salary, 2) }}</td>
                <td>Total Deductions</td>
                <td class="amt" style="color: #e11d48;">Rs. {{ number_format($payroll->total_deductions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Net Salary Highlight -->
    <div class="net-box">
        <div class="net-title">Net Take-Home Salary Disbursed</div>
        <div class="net-amt">INR {{ number_format($payroll->net_salary, 2) }}</div>
    </div>

    <!-- Signatures -->
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-line">Employee Signature</div>
            </td>
            <td>
                <div class="sign-line">Principal / Authorized Signatory</div>
            </td>
        </tr>
    </table>

</body>
</html>
