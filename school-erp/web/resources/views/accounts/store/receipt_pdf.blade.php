<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Store Bill - {{ $sale->bill_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 12px;
            font-size: 10.5px;
            line-height: 1.4;
        }
        .bill-card {
            border: 1.5px solid #d97706;
            border-radius: 6px;
            padding: 14px;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 1.5px solid #d97706;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .crest {
            width: 44px;
            height: 44px;
            background-color: #d97706;
            color: #ffffff;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            line-height: 44px;
            border-radius: 5px;
        }
        .title {
            font-size: 14px;
            font-weight: 800;
            color: #b45309;
            text-transform: uppercase;
        }
        .sub {
            font-size: 8.5px;
            color: #64748b;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .info-table td {
            padding: 2px 4px;
        }
        .label {
            color: #64748b;
            font-weight: 600;
            width: 20%;
        }
        .val {
            font-weight: 700;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 6px;
            font-size: 9px;
            text-transform: uppercase;
            text-align: left;
            color: #475569;
        }
        .items-table td {
            border-bottom: 1px solid #f1f5f9;
            padding: 5px 6px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .total-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            padding: 8px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .signatures {
            width: 100%;
            margin-top: 20px;
        }
        .signatures td {
            width: 50%;
            padding-top: 25px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
            font-size: 8.5px;
            color: #475569;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="bill-card">
        <table class="header-table">
            <tr>
                <td style="width: 50px;">
                    <div class="crest">TS</div>
                </td>
                <td>
                    <div class="title">Test Subject Higher Secondary School</div>
                    <div class="sub">Official School Store & Uniforms Division &bull; Pudukkottai, Tamil Nadu</div>
                    <div class="sub">Retail Cash Memo / Purchase Bill</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-family: monospace; font-size: 11px; font-weight: bold; color: #b45309;">
                        {{ $sale->bill_no }}
                    </div>
                    <div style="font-size: 9px; color: #64748b;">Date: {{ $sale->sale_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label">Customer / Student:</td>
                <td class="val">{{ $sale->customer_name }}</td>
                <td class="label">Buyer Type:</td>
                <td class="val" style="text-transform: uppercase;">{{ $sale->customer_type }}</td>
            </tr>
            @if($sale->student)
                <tr>
                    <td class="label">Admission No:</td>
                    <td class="val" style="font-family: monospace;">{{ $sale->student->admission_no }}</td>
                    <td class="label">Class & Sec:</td>
                    <td class="val">Class {{ $sale->student->schoolClass?->name }} - {{ $sale->student->section?->name }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Payment Mode:</td>
                <td class="val">{{ $sale->payment_method }}</td>
                <td class="label">Cashier:</td>
                <td class="val">{{ $sale->cashier?->name ?? 'Store Cashier' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 52%;">Article Item Description</th>
                    <th style="width: 12%; text-align: center;">Qty</th>
                    <th style="width: 14%;" class="text-right">Rate (₹)</th>
                    <th style="width: 14%;" class="text-right">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $idx => $it)
                    <tr>
                        <td style="color: #94a3b8; font-family: monospace;">{{ $idx + 1 }}</td>
                        <td style="font-weight: 600;">{{ $it->storeItem?->name ?? 'Article' }}</td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $it->quantity }}</td>
                        <td class="text-right" style="font-family: monospace;">{{ number_format($it->unit_price, 2) }}</td>
                        <td class="text-right" style="font-family: monospace; font-weight: bold;">{{ number_format($it->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <table style="width: 100%;">
                @if($sale->discount > 0)
                    <tr>
                        <td style="font-size: 9px; color: #64748b;">Subtotal Amount:</td>
                        <td class="text-right" style="font-family: monospace; font-weight: bold;">₹ {{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px; color: #b45309;">Discount Applied:</td>
                        <td class="text-right" style="font-family: monospace; font-weight: bold; color: #b45309;">- ₹ {{ number_format($sale->discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="font-size: 11px; font-weight: 800; color: #b45309; text-transform: uppercase;">
                        Net Amount Paid:
                    </td>
                    <td class="text-right" style="font-size: 14px; font-weight: 900; font-family: monospace; color: #b45309;">
                        INR ₹ {{ number_format($sale->total_amount, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="signatures">
            <tr>
                <td>Customer / Student Signature</td>
                <td>Store Manager / Cashier Signature</td>
            </tr>
        </table>
    </div>

</body>
</html>
