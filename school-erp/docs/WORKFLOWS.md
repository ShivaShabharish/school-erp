# APPLICATION WORKFLOWS

## 1. Admission to Student Enrolment
1. Enquiry registered &rarr; Review by Office / Principal.
2. Direct Admission form filled (Student biodata, parent contact, class and section selection).
3. System assigns unique Admission Number (`ADM-2026-XXXX`).
4. Automated creation of student record and corresponding parent user account for portal login.
5. Fee dues initialized for the 3 academic terms.

## 2. Biometric Staff Attendance & Live Tracking
1. Biometric punch received via `POST /api/v1/biometric/punch` (or demo simulator).
2. If arrival &le; 08:45 AM &rarr; Status `present` (on-time).
3. If arrival between 08:46 AM and 09:15 AM &rarr; Status `late`.
4. If no punch recorded by 09:30 AM and no approved leave &rarr; Flagged `absent`.
5. Admin live dashboard tile auto-updates every 10 seconds via polling endpoint.

## 3. Fee Collection & Sequential Receipt Generation
1. Accountant selects Student &rarr; View term dues and concession breakdown.
2. Select installment &rarr; Choose Payment Method (Cash, UPI, Cheque, Bank Transfer).
3. Database transaction locks record, generates atomic sequential receipt number (`SCH/2026-27/REC-XXXXX`).
4. Instant PDF receipt generated and available for print/download.
5. Income ledger credited automatically under Fee Collection category.

## 4. Timetable Scheduling & Teacher Substitution
1. Class timetable generated across 8 periods Monday through Saturday.
2. Automated clash validation prevents assigning a teacher or room to multiple concurrent periods.
3. If a teacher is marked absent/on leave, substitution recommendation engine finds teachers free in that period and provides one-click substitution assignment.
