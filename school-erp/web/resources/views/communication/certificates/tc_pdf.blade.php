<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer Certificate - {{ $certificate->certificate_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 11.5px;
        }
        .outer-border {
            border: 3px double #047857;
            padding: 16px;
            min-height: 960px;
            position: relative;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #047857;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .school-name {
            font-size: 19px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .school-sub {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }
        .tc-title {
            display: inline-block;
            background-color: #047857;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 4px 20px;
            border-radius: 3px;
            margin-top: 8px;
        }
        .tc-meta {
            width: 100%;
            margin-bottom: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .tc-meta td {
            padding: 2px 0;
        }
        .tc-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .tc-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            vertical-align: middle;
        }
        .tc-col-sl {
            width: 6%;
            text-align: center;
            font-weight: bold;
            background-color: #f8fafc;
        }
        .tc-col-field {
            width: 44%;
            font-weight: 600;
            color: #334155;
            background-color: #f8fafc;
        }
        .tc-col-val {
            width: 50%;
            font-weight: bold;
            color: #0f172a;
        }
        .footer-signatures {
            margin-top: 35px;
            width: 100%;
        }
        .seal-box {
            display: inline-block;
            width: 80px;
            height: 80px;
            border: 1.5px dashed #94a3b8;
            border-radius: 50%;
            text-align: center;
            line-height: 80px;
            color: #94a3b8;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .sig-col {
            text-align: center;
            vertical-align: bottom;
        }
        .sig-line {
            width: 130px;
            border-top: 1px solid #1e293b;
            margin: 0 auto 4px auto;
        }
        .sig-title {
            font-weight: bold;
            font-size: 10.5px;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="header">
            <div class="school-name">TEST SUBJECT HIGHER SECONDARY SCHOOL</div>
            <div class="school-sub">Recognized by the Director of School Education, Govt. of Tamil Nadu</div>
            <div class="school-sub">Tamil Nadu State Board Curriculum • Affiliation No: TN-PDK-2024-HSS</div>
            <div class="school-sub">Main Road, Alangudi Gate, Pudukkottai - 622 001, Tamil Nadu</div>
            <div>
                <span class="tc-title">TRANSFER CERTIFICATE</span>
            </div>
        </div>

        @php
            $details = $certificate->details ?? [];
            $dob = \Carbon\Carbon::parse($certificate->student->date_of_birth);
            $leavingDate = !empty($details['leaving_date']) ? \Carbon\Carbon::parse($details['leaving_date'])->format('d-m-Y') : \Carbon\Carbon::parse($certificate->issue_date)->format('d-m-Y');
        @endphp

        <table class="tc-meta">
            <tr>
                <td style="text-align: left; width: 50%;">
                    T.C. No: <span style="color: #047857;">{{ $certificate->certificate_no }}</span>
                </td>
                <td style="text-align: right; width: 50%;">
                    Admission No: <span style="color: #047857;">{{ $certificate->student->admission_number }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    EMIS No: {{ $certificate->student->emis_number ?: '33220100' . sprintf('%06d', $certificate->student->id) }}
                </td>
                <td style="text-align: right;">
                    Date of Issue: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d-m-Y') }}
                </td>
            </tr>
        </table>

        <table class="tc-table">
            <tr>
                <td class="tc-col-sl">1</td>
                <td class="tc-col-field">Name of the Pupil (in Block Letters)</td>
                <td class="tc-col-val">{{ strtoupper($certificate->student->full_name) }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">2</td>
                <td class="tc-col-field">Father's Name</td>
                <td class="tc-col-val">{{ strtoupper($certificate->student->father_name ?: 'N/A') }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">3</td>
                <td class="tc-col-field">Mother's Name</td>
                <td class="tc-col-val">{{ strtoupper($certificate->student->mother_name ?: 'N/A') }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">4</td>
                <td class="tc-col-field">Nationality & Religion</td>
                <td class="tc-col-val">Indian — {{ ucfirst($certificate->student->religion ?: 'Hindu') }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">5</td>
                <td class="tc-col-field">Community & Caste</td>
                <td class="tc-col-val">{{ strtoupper($certificate->student->community ?: 'BC') }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">6</td>
                <td class="tc-col-field">Sex</td>
                <td class="tc-col-val">{{ ucfirst($certificate->student->gender) }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">7</td>
                <td class="tc-col-field">Date of Birth (as per Admission Register)<br><span style="font-size: 9px; font-weight: normal; color: #64748b;">(in figures & in words)</span></td>
                <td class="tc-col-val">
                    {{ $dob->format('d-m-Y') }}<br>
                    <span style="font-size: 9.5px; font-weight: normal; font-style: italic;">({{ $dob->format('jS F, Y') }})</span>
                </td>
            </tr>
            <tr>
                <td class="tc-col-sl">8</td>
                <td class="tc-col-field">Personal Marks of Identification</td>
                <td class="tc-col-val">{{ $details['identification_marks'] ?? '1. A mole on the right collar bone; 2. A scar on left forearm' }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">9</td>
                <td class="tc-col-field">Date of Admission & Standard</td>
                <td class="tc-col-val">{{ \Carbon\Carbon::parse($certificate->student->admission_date)->format('d-m-Y') }} — Class {{ $certificate->student->schoolClass?->name }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">10</td>
                <td class="tc-col-field">Standard in which the pupil was studying at the time of leaving</td>
                <td class="tc-col-val">Class {{ $certificate->student->schoolClass?->name }} ({{ $certificate->student->section?->name }})</td>
            </tr>
            <tr>
                <td class="tc-col-sl">11</td>
                <td class="tc-col-field">Medium of Instruction</td>
                <td class="tc-col-val">English</td>
            </tr>
            <tr>
                <td class="tc-col-sl">12</td>
                <td class="tc-col-field">Whether qualified for promotion to higher standard</td>
                <td class="tc-col-val">Refer to Marksheet / Promotion Granted</td>
            </tr>
            <tr>
                <td class="tc-col-sl">13</td>
                <td class="tc-col-field">Whether all school dues have been cleared</td>
                <td class="tc-col-val">{{ $details['fee_dues_cleared'] ?? 'Yes' }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">14</td>
                <td class="tc-col-field">Date on which the pupil actually left the school</td>
                <td class="tc-col-val">{{ $leavingDate }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">15</td>
                <td class="tc-col-field">Reason for leaving the school</td>
                <td class="tc-col-val">{{ $details['reason_for_leaving'] ?? $certificate->reason ?? 'Course Completed / Parent Transfer' }}</td>
            </tr>
            <tr>
                <td class="tc-col-sl">16</td>
                <td class="tc-col-field">General Conduct and Character</td>
                <td class="tc-col-val">{{ $details['conduct'] ?? 'GOOD' }}</td>
            </tr>
        </table>

        <table class="footer-signatures">
            <tr>
                <td class="sig-col" style="width: 25%;">
                    <div class="sig-line"></div>
                    <div class="sig-title">Prepared By</div>
                    <div style="font-size: 9px; color: #64748b;">Office Clerk</div>
                </td>
                <td class="sig-col" style="width: 25%;">
                    <div class="sig-line"></div>
                    <div class="sig-title">Checked By</div>
                    <div style="font-size: 9px; color: #64748b;">Academic In-charge</div>
                </td>
                <td class="sig-col" style="width: 25%;">
                    <div class="seal-box">
                        School Seal
                    </div>
                </td>
                <td class="sig-col" style="width: 25%;">
                    <div class="sig-line"></div>
                    <div class="sig-title">Principal / Headmaster</div>
                    <div style="font-size: 9px; color: #64748b;">Test Subject HSS</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
