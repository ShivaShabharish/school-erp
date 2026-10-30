<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Report Card - {{ $student->admission_no }}</title>
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
            padding-bottom: 10px;
            margin-bottom: 14px;
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
            margin-top: 1px;
        }
        .title-badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-top: 6px;
            letter-spacing: 1px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .student-table td {
            padding: 3px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .s-label {
            color: #64748b;
            font-weight: bold;
            width: 18%;
        }
        .s-val {
            color: #0f172a;
            font-weight: bold;
            width: 32%;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .marks-table th {
            background-color: #047857;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 8px;
            text-transform: uppercase;
            text-align: left;
        }
        .marks-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            font-size: 10px;
        }
        .amt {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .total-card {
            background-color: #f0fdf4;
            border: 1.5px solid #059669;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .total-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .total-grid td {
            padding: 2px 4px;
            font-size: 11px;
        }
        .legend-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
            color: #64748b;
        }
        .legend-table td {
            border: 1px solid #e2e8f0;
            padding: 3px 4px;
            text-align: center;
        }
        .sign-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .sign-table td {
            width: 33.33%;
            vertical-align: bottom;
            text-align: center;
            padding-top: 35px;
        }
        .sign-line {
            border-top: 1px dashed #94a3b8;
            width: 80%;
            margin: 0 auto;
            padding-top: 4px;
            font-size: 9px;
            font-weight: bold;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="school-name">Test Subject Higher Secondary School</div>
        <div class="school-sub">Affiliated to Tamil Nadu State Board of Education &bull; Code: TN-PDK-0428</div>
        <div class="school-sub">Main Campus, Collectorate Road, Pudukkottai - 622001, Tamil Nadu</div>
        <div class="title-badge">CCE EVALUATION REPORT CARD &bull; {{ strtoupper($exam->name) }}</div>
    </div>

    <!-- Student Bio -->
    <table class="student-table">
        <tr>
            <td class="s-label">Student Name:</td>
            <td class="s-val">{{ $student->full_name }}</td>
            <td class="s-label">Admission No:</td>
            <td class="s-val">{{ $student->admission_no }}</td>
        </tr>
        <tr>
            <td class="s-label">Class & Section:</td>
            <td class="s-val">{{ $student->schoolClass?->name }} - Section {{ $student->section?->name }}</td>
            <td class="s-label">Roll Number:</td>
            <td class="s-val">{{ $student->roll_number ?: '-' }}</td>
        </tr>
        <tr>
            <td class="s-label">Father / Guardian:</td>
            <td class="s-val">{{ $student->father_name }}</td>
            <td class="s-label">Attendance:</td>
            <td class="s-val">{{ $attendance_percentage }}% Present</td>
        </tr>
        <tr>
            <td class="s-label">Academic Year:</td>
            <td class="s-val">2026-2027</td>
            <td class="s-label">Result Status:</td>
            <td class="s-val" style="color: #047857;">PASSED (QUALIFIED)</td>
        </tr>
    </table>

    <!-- Subject Scores Table -->
    <table class="marks-table">
        <thead>
            <tr>
                <th style="width: 12%;">Code</th>
                <th style="width: 38%;">Academic Subject</th>
                <th style="width: 12%;" class="amt">Max Marks</th>
                <th style="width: 12%;" class="amt">Pass Marks</th>
                <th style="width: 14%;" class="amt">Marks Scored</th>
                <th style="width: 12%;" class="center">CCE Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $sub)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $sub['subject_code'] }}</td>
                    <td style="font-weight: bold;">{{ $sub['subject_name'] }}</td>
                    <td class="amt">{{ $sub['max_marks'] }}</td>
                    <td class="amt">{{ $sub['pass_marks'] }}</td>
                    <td class="amt" style="color: #047857;">
                        {{ $sub['is_absent'] ? 'ABSENT' : number_format($sub['marks_obtained'], 1) }}
                    </td>
                    <td class="center" style="font-weight: bold;">
                        {{ $sub['grade'] }}
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2">GRAND AGGREGATE TOTAL</td>
                <td class="amt">{{ $total_max }}</td>
                <td class="amt">-</td>
                <td class="amt" style="color: #047857;">{{ number_format($total_obtained, 1) }}</td>
                <td class="center" style="color: #047857;">{{ $overall_grade }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Total Score Summary Card -->
    <div class="total-card">
        <table class="total-grid">
            <tr>
                <td><strong>Aggregate Percentage:</strong> <span style="font-family: monospace; font-size: 13px; color: #047857; font-weight: bold;">{{ $overall_percentage }}%</span></td>
                <td><strong>Cumulative GPA (CGPA):</strong> <span style="font-family: monospace; font-size: 13px; color: #047857; font-weight: bold;">{{ $gpa }} / 10.0</span></td>
                <td><strong>Performance:</strong> <span style="font-weight: bold; color: #065f46;">{{ $overall_remark }}</span></td>
            </tr>
        </table>
    </div>

    <!-- TN Board CCE Grading Scale Legend -->
    <table class="legend-table">
        <tr>
            <td style="font-weight: bold; background-color: #f1f5f9;">Tamil Nadu CCE Scale</td>
            <td>A1 (91-100) &bull; 10.0</td>
            <td>A2 (81-90) &bull; 9.0</td>
            <td>B1 (71-80) &bull; 8.0</td>
            <td>B2 (61-70) &bull; 7.0</td>
            <td>C1 (51-60) &bull; 6.0</td>
            <td>C2 (41-50) &bull; 5.0</td>
            <td>D (33-40) &bull; 4.0</td>
            <td>E (&lt;33) &bull; Needs Imp.</td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-line">Class Teacher Signature</div>
            </td>
            <td>
                <div class="sign-line">Parent / Guardian Signature</div>
            </td>
            <td>
                <div class="sign-line">Principal Seal & Signature</div>
            </td>
        </tr>
    </table>

</body>
</html>
