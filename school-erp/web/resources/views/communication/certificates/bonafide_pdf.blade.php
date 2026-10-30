<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bonafide Certificate - {{ $certificate->certificate_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 13px;
        }
        .outer-border {
            border: 4px double #047857;
            padding: 24px;
            min-height: 920px;
            position: relative;
            box-sizing: border-box;
        }
        .inner-border {
            border: 1px solid #cbd5e1;
            padding: 24px;
            min-height: 870px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #047857;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .school-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }
        .cert-badge {
            display: inline-block;
            background-color: #047857;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 6px 24px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .meta-strip {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 30px;
        }
        .meta-strip td {
            font-size: 12px;
            color: #334155;
        }
        .cert-body {
            font-size: 14.5px;
            line-height: 2.2;
            text-align: justify;
            margin-top: 30px;
            color: #1e293b;
        }
        .highlight {
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px dashed #64748b;
            padding: 0 4px;
        }
        .purpose-box {
            margin-top: 30px;
            padding: 12px 16px;
            background-color: #f8fafc;
            border-left: 4px solid #047857;
            font-size: 13px;
        }
        .footer-signatures {
            position: absolute;
            bottom: 40px;
            left: 45px;
            right: 45px;
        }
        .seal-box {
            display: inline-block;
            width: 110px;
            height: 110px;
            border: 2px dashed #94a3b8;
            border-radius: 50%;
            text-align: center;
            line-height: 110px;
            color: #94a3b8;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .sig-col {
            text-align: center;
            vertical-align: bottom;
        }
        .sig-line {
            width: 150px;
            border-top: 1px solid #1e293b;
            margin: 0 auto 5px auto;
        }
        .sig-title {
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="inner-border">
            <div class="header">
                <div class="school-name">TEST SUBJECT HIGHER SECONDARY SCHOOL</div>
                <div class="school-sub">Recognized by Govt. of Tamil Nadu • Tamil Nadu State Board (Affiliation No: TN-PDK-2024-HSS)</div>
                <div class="school-sub">Main Road, Alangudi Gate, Pudukkottai - 622 001, Tamil Nadu</div>
                <div class="school-sub">Phone: +91 4322 221234 | Email: info@testsubjecthss.edu.in</div>
                <div>
                    <span class="cert-badge">BONAFIDE CERTIFICATE</span>
                </div>
            </div>

            <table class="meta-strip">
                <tr>
                    <td style="text-align: left;">
                        <strong>Certificate No:</strong> <span style="color: #047857; font-weight: bold;">{{ $certificate->certificate_no }}</span>
                    </td>
                    <td style="text-align: right;">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d-m-Y') }}
                    </td>
                </tr>
            </table>

            <div class="cert-body">
                This is to certify that Master / Kumari 
                <span class="highlight">{{ strtoupper($certificate->student->full_name) }}</span>, 
                Admission Number <span class="highlight">{{ $certificate->student->admission_number }}</span>, 
                Son / Daughter of Thiru / Tmt. <span class="highlight">{{ strtoupper($certificate->student->father_name ?: $certificate->student->mother_name ?: 'Guardian') }}</span>, 
                is / was a bona fide student of this institution, currently studying in 
                <span class="highlight">Class {{ $certificate->student->schoolClass?->name }} (Section {{ $certificate->student->section?->name }})</span> 
                during the Academic Year <span class="highlight">{{ $certificate->academicYear?->name ?? '2026-2027' }}</span>.
                <br><br>
                According to the Admission Register of the school, his / her Date of Birth is 
                <span class="highlight">{{ \Carbon\Carbon::parse($certificate->student->date_of_birth)->format('d-m-Y') }}</span>.
                <br><br>
                His / Her character and conduct during the stay in the school have been 
                <span class="highlight">GOOD</span>.
            </div>

            <div class="purpose-box">
                <strong>Purpose of Certificate:</strong> 
                <span>{{ $certificate->reason ?: 'Higher Education / Official Documentation / Scholarship' }}</span>
            </div>

            <table style="width: 100%;" class="footer-signatures">
                <tr>
                    <td class="sig-col" style="width: 33%;">
                        <div class="sig-line"></div>
                        <div class="sig-title">Prepared By</div>
                        <div style="font-size: 10px; color: #64748b;">Office Clerk</div>
                    </td>
                    <td class="sig-col" style="width: 34%;">
                        <div class="seal-box">
                            Institution Seal
                        </div>
                    </td>
                    <td class="sig-col" style="width: 33%;">
                        <div class="sig-line"></div>
                        <div class="sig-title">Principal / Headmaster</div>
                        <div style="font-size: 10px; color: #64748b;">Test Subject HSS</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
