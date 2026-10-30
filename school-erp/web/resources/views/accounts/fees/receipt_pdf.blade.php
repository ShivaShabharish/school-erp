<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Fee Receipt - {{ $receipt->receipt_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.4;
        }
        .receipt-card {
            border: 2px solid #047857;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-crest {
            width: 50px;
            height: 50px;
            background-color: #047857;
            color: #ffffff;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
            line-height: 50px;
            border-radius: 6px;
        }
        .school-title {
            font-size: 16px;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 9px;
            color: #64748b;
        }
        .badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
        }
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 10.5px;
        }
        .info-table td {
            padding: 3px 4px;
        }
        .label {
            color: #64748b;
            font-weight: 600;
            width: 18%;
        }
        .val {
            font-weight: 700;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            color: #475569;
        }
        .items-table td {
            border-bottom: 1px solid #f1f5f9;
            padding: 6px 8px;
            font-size: 10.5px;
        }
        .text-right {
            text-align: right;
        }
        .total-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
        }
        .signatures td {
            width: 50%;
            padding-top: 30px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
            font-size: 9.5px;
            color: #475569;
            font-weight: bold;
        }
        .cut-line {
            text-align: center;
            border-top: 1px dashed #94a3b8;
            margin: 15px 0;
            position: relative;
        }
        .cut-label {
            position: absolute;
            top: -9px;
            background: #fff;
            padding: 0 10px;
            font-size: 8px;
            color: #94a3b8;
            font-family: monospace;
        }
    </style>
</head>
<body>

    <!-- STUDENT / PARENT COPY -->
    <div class="receipt-card">
        <table class="header-table">
            <tr>
                <td style="width: 60px;">
                    <div class="school-crest">TS</div>
                </td>
                <td>
                    <div class="school-title">Test Subject Higher Secondary School</div>
                    <div class="school-sub">Affiliated to Tamil Nadu State Board &bull; Pudukkottai, Tamil Nadu</div>
                    <div class="school-sub">Official Academic Fee Receipt &bull; Academic Year {{ $receipt->academicYear?->name ?? '2026-2027' }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="badge" style="background-color: #ecfdf5; border-color: #047857; color: #047857;">STUDENT COPY</div>
                    <div style="font-family: monospace; font-size: 11px; font-weight: bold; margin-top: 4px; color: #047857;">
                        {{ $receipt->receipt_no }}
                    </div>
                    <div style="font-size: 9.5px; color: #64748b;">Date: {{ $receipt->receipt_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label">Student Name:</td>
                <td class="val">{{ $receipt->student?->full_name }}</td>
                <td class="label">Admission No:</td>
                <td class="val" style="font-family: monospace;">{{ $receipt->student?->admission_no }}</td>
            </tr>
            <tr>
                <td class="label">Class & Sec:</td>
                <td class="val">Class {{ $receipt->student?->schoolClass?->name }} - {{ $receipt->student?->section?->name }}</td>
                <td class="label">Roll Number:</td>
                <td class="val">{{ $receipt->student?->roll_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Payment Mode:</td>
                <td class="val">{{ $receipt->payment_method }} {{ $receipt->transaction_reference ? '(' . $receipt->transaction_reference . ')' : '' }}</td>
                <td class="label">Collected By:</td>
                <td class="val">{{ $receipt->collector?->name ?? 'Cash Desk' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 60%;">Fee Component / Term Particulars</th>
                    <th style="width: 30%;" class="text-right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipt->items as $index => $item)
                    <tr>
                        <td style="color: #94a3b8; font-family: monospace;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $item->description }}</td>
                        <td class="text-right" style="font-family: monospace; font-weight: 700;">
                            {{ number_format($item->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td style="color: #94a3b8; font-family: monospace;">1</td>
                        <td style="font-weight: 600;">Tuition & Term Fee Collection</td>
                        <td class="text-right" style="font-family: monospace; font-weight: 700;">
                            {{ number_format($receipt->amount_paid, 2) }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-box">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <span style="font-size: 9.5px; text-transform: uppercase; color: #047857; font-weight: bold;">Total Amount Received:</span>
                        <div style="font-size: 14px; font-weight: 900; color: #047857; font-family: monospace;">
                            INR ₹ {{ number_format($receipt->amount_paid, 2) }}
                        </div>
                    </td>
                    <td class="text-right" style="vertical-align: middle;">
                        <span style="font-size: 9px; color: #64748b;">Remaining Term Dues:</span>
                        <span style="font-family: monospace; font-weight: bold; color: {{ ($ledger['balance_due'] ?? 0) > 0 ? '#e11d48' : '#047857' }};">
                            ₹ {{ number_format($ledger['balance_due'] ?? 0, 2) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        @if($receipt->notes)
            <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                <strong>Remarks:</strong> {{ $receipt->notes }}
            </div>
        @endif

        <table class="signatures">
            <tr>
                <td>Cashier / Accountant Signature</td>
                <td>Principal / Authorized Signatory</td>
            </tr>
        </table>
    </div>

    <!-- Perforation Cut Line -->
    <div class="cut-line">
        <span class="cut-label">&bull;&bull;&bull; TEAR HERE &bull; SCHOOL OFFICE COUNTERFOIL &bull;&bull;&bull;</span>
    </div>

    <!-- OFFICE COUNTERFOIL -->
    <div class="receipt-card" style="border-color: #475569; padding: 12px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <strong style="color: #047857;">TEST SUBJECT HSS &bull; OFFICE COPY</strong>
                    <div style="font-size: 9px; color: #64748b;">Receipt: <span style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ $receipt->receipt_no }}</span> | Date: {{ $receipt->receipt_date->format('d/m/Y') }}</div>
                    <div style="font-size: 9px;">Student: <strong>{{ $receipt->student?->full_name }}</strong> ({{ $receipt->student?->admission_no }}) &bull; Class {{ $receipt->student?->schoolClass?->name }}-{{ $receipt->student?->section?->name }}</div>
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <div style="font-size: 13px; font-weight: 900; font-family: monospace; color: #047857;">
                        ₹ {{ number_format($receipt->amount_paid, 2) }}
                    </div>
                    <div style="font-size: 8.5px; color: #64748b;">Mode: {{ $receipt->payment_method }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
