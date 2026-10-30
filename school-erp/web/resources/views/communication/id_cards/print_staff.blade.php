<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Staff ID Cards - Test Subject HSS</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .no-print {
            background-color: #ffffff;
            padding: 12px 24px;
            margin-bottom: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-print {
            background-color: #d97706;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #b45309;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            page-break-after: always;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 86mm);
            grid-gap: 8mm 12mm;
            justify-content: center;
        }

        .id-card {
            width: 86mm;
            height: 54mm;
            border: 1.5px solid #d97706;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #ffffff;
            position: relative;
        }
        .id-card-header {
            background-color: #0f172a;
            color: #ffffff;
            text-align: center;
            padding: 3px 6px;
            border-bottom: 2px solid #d97706;
        }
        .id-card-header .title {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .id-card-header .sub {
            font-size: 6pt;
            color: #fbbf24;
        }

        .id-card-body {
            display: flex;
            padding: 4px 6px;
            gap: 6px;
            align-items: center;
            flex: 1;
        }
        .avatar-box {
            width: 22mm;
            height: 26mm;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            font-weight: bold;
            color: #d97706;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .info-col {
            flex: 1;
            font-size: 6.5pt;
            line-height: 1.35;
        }
        .staff-name {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .info-row {
            display: flex;
            margin-bottom: 1px;
        }
        .info-label {
            width: 32px;
            color: #64748b;
            font-weight: normal;
        }
        .info-val {
            font-weight: bold;
            color: #1e293b;
        }

        .id-card-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 2px 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 5.5pt;
        }
        .barcode {
            font-family: monospace;
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 8mm 6mm;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <h2 style="margin: 0 0 4px 0; font-size: 16px;">Staff Identity Cards Print Sheet</h2>
            <div style="font-size: 12px; color: #64748b;">
                Found {{ $staffMembers->count() }} staff members • Formatted for 8 CR-80 cards per A4 page
            </div>
        </div>
        <button class="btn-print" onclick="window.print()">
            Print All Staff Cards
        </button>
    </div>

    @foreach($staffMembers->chunk(8) as $chunk)
        <div class="page">
            <div class="grid-container">
                @foreach($chunk as $staff)
                    <div class="id-card">
                        <div class="id-card-header">
                            <div class="title">Test Subject HSS</div>
                            <div class="sub">Staff Identification Card</div>
                        </div>

                        <div class="id-card-body">
                            <div class="avatar-box">
                                <span>{{ substr($staff->user->first_name ?? 'S', 0, 1) }}{{ substr($staff->user->last_name ?? 'T', 0, 1) }}</span>
                            </div>

                            <div class="info-col">
                                <div class="staff-name">{{ strtoupper($staff->user->name ?? $staff->employee_code) }}</div>
                                <div class="info-row">
                                    <span class="info-label">Desig:</span>
                                    <span class="info-val" style="color: #b45309;">{{ $staff->designation ?? 'Teacher' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Dept:</span>
                                    <span class="info-val">{{ $staff->department ?? 'Academics' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Emp ID:</span>
                                    <span class="info-val">{{ $staff->employee_code }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Blood:</span>
                                    <span class="info-val" style="color: #dc2626;">{{ $staff->blood_group ?: 'B+' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Phone:</span>
                                    <span class="info-val">{{ $staff->user->phone ?: '+91 98400 11223' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="id-card-footer">
                            <span class="barcode">*{{ $staff->employee_code }}*</span>
                            <span style="font-weight: bold; color: #0f172a;">VALID 2026-27</span>
                            <span>Principal Sign</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</body>
</html>
