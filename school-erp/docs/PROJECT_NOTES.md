# PROJECT NOTES & LIVING MEMORY

## Institution Profile
- **Institution Name**: Test Subject Higher Secondary School
- **Affiliation**: Tamil Nadu State Board (Continuous Comprehensive Evaluation - CCE pattern)
- **Location**: Pudukkottai, Tamil Nadu, India
- **Currency**: INR (₹)
- **Academic Year**: April to March (Current: 2026-2027)
- **Languages / Medium**: English Medium, Tamil (Second Language), Hindi / French (Third Language)
- **Classes**: LKG to 12th Standard with sections (A, B)
- **Working Days**: Monday to Saturday (2nd and 4th Saturdays holidays, Sundays off)
- **Receipt Numbering**: Academic Year Prefixed: `SCH/2026-27/REC-XXXXX` (Fees) & `SCH/2026-27/STR-XXXXX` (Store)

---

## Technology Stack
- **Framework**: Laravel 11 / 12 on PHP 8.2+
- **Database**: MySQL 8.0 (utf8mb4_unicode_ci)
- **Frontend**: Blade Components, Tailwind CSS (Vite), Alpine.js, Chart.js, Lucide Icons
- **PDF Engine**: Barryvdh Laravel DomPDF
- **API & Auth**: Laravel Sanctum tokens for API, Session auth with RoleMiddleware for Web
- **Realtime / Dashboard**: 10-second lightweight JSON polling endpoints

---

## Design System & Theme Rules
- **Primary Color**: Deep Emerald (`#047857`, `#065F46`, `#064E3B`)
- **Accent Color**: Warm Amber (`#D97706`, `#F59E0B`)
- **Neutrals**: Slate palette (`#F8FAFC` to `#0F172A`)
- **Typography**: Plus Jakarta Sans (headings) + Inter (body & UI)
- **Dark Mode**: Native Tailwind dark mode support via `.dark` class
- **Grid & Spacing**: 8px modular scale, generous whitespace, AA contrast compliance
- **Top Navigation**: Persistent "Accounts & Billing" button in top-right opening the billing area
- **Zero Icons as Emojis**: SVG Lucide icons exclusively

---

## Role & Permission Matrix
| Role | Admin Dashboard | Students & Admissions | Staff & Biometric | Academics & Exams | Accounts & Billing | Settings & Audit |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Chairman** | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write |
| **Principal** | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write | Full Read/Write |
| **Accountant**| Denied | View Only | View Only | Denied | Full Read/Write | Denied |
| **Teacher** | Denied | Class View | Own Attendance / Leaves | Timetable, Marks, Attendance | Denied | Denied |
| **Staff** | Denied | Denied | Own Attendance / Leaves | Denied | Denied | Denied |
| **Parent** | Denied | Own Ward View | Denied | Ward Marks & Homework | Ward Fee Payment / Receipts | Denied |

---

## Demo Credentials (Demo Only)
- **Chairman**: `chairman@school.test` | `Password@123`
- **Principal**: `principal@school.test` | `Password@123`
- **Accountant**: `accountant@school.test` | `Password@123`
- **Teacher**: `teacher@school.test` | `Password@123`
- **Parent**: `parent@school.test` | `Password@123`

---

## Feature Location Map ("Which Feature Lives Where")
- **Authentication**: `app/Http/Controllers/Auth/`, `resources/views/auth/login.blade.php`
- **Role Middleware**: `app/Http/Middleware/RoleMiddleware.php`
- **Admin Dashboard**: `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard.blade.php`
- **Business Reporting & Aggregations**: `app/Services/ReportService.php`
- **Accounts & Billing**: `app/Http/Controllers/Accounts/`, `resources/views/accounts/dashboard.blade.php`
- **Students & Admissions**: `app/Http/Controllers/Students/`, `resources/views/students/`
- **Staff, Biometric & Leaves**: `app/Http/Controllers/Staff/`, `resources/views/staff/dashboard.blade.php`
- **Parent Portal**: `app/Http/Controllers/Students/ParentDashboardController.php`, `resources/views/students/parent_dashboard.blade.php`
- **Academics & Timetables**: `app/Http/Controllers/Academics/`, `resources/views/academics/`
- **Communication & Records**: `app/Http/Controllers/Communication/`, `resources/views/communication/`
- **Settings & API Config**: `app/Http/Controllers/Settings/`, `resources/views/settings/`
- **Biometric Punch API**: `POST /api/v1/biometric/punch` &rarr; `app/Http/Controllers/Api/V1/`
- **Live Metrics Polling**: `GET /admin/metrics/live` &rarr; `Admin\DashboardController@liveMetrics`
- **System Health**: `GET /health` &rarr; `routes/web.php`

---

## Phase Checklist
- [x] **P1 Foundation**: Environment verified, database connected, Laravel 11 scaffolded, auth, roles, layout shell, design system, /health endpoint, seeders, scripts. Completed & fully tested.
- [x] **P2 Admin Dashboard**: Live tiles (students present, staff present, staff on leave, today's collection, month margin), dynamic class-wise strength/turnout table (Classes LKG-12), 7-day attendance trend chart, operational alerts (late staff, fee defaulters, low stock), 10s auto-refresh polling endpoint. Completed & fully tested.
- [x] **P3 Students & Admissions**: Enquiry to admission pipeline with conversion modal, comprehensive 360° student profile with document verification & fee receipts history, batch class-to-class promotion wizard with audit logs, CSV import/export, and class/section rosters. Completed & fully tested.
- [x] **P4 Staff & Attendance**: Staff directory & 360 profiles, live attendance logs with reporting cutoff (08:30 AM), biometric punch API (`POST /api/v1/biometric/punch`), admin biometric simulator console, leave management with quotas (CL, ML, EL) & approval workflow, and monthly salary sheet with DomPDF payslips. Completed & fully tested.
- [x] **P5 Academics**: Class and teacher weekly timetables with automated clash detection (period/day/teacher overlap), intelligent substitution recommendation engine cross-referencing daily biometric punches, Tamil Nadu CCE examinations & scheduling, spreadsheet marks entry sheet, CCE progress report card PDF generation, homework assignment board, and daily section roll-call register. Completed & fully tested.
- [x] **P6 Accounts & Billing**: Dedicated finance division portal with 10s live metrics polling; 3-term installment fee structures (Term 1, Term 2, Term 3) per class (LKG to 12) with automatic student allocation; Cashier Collection Counter with student search, dues schedule, and sequential DomPDF receipts (`SCH/2026-27/REC-XXXXX`); fee concessions & scholarship grants (sibling, staff ward, merit, RTE); School Store catalog & POS billing register with atomic inventory decrement, profit margin calculation, and DomPDF bills (`SCH/2026-27/STR-XXXXX`); double-entry expense ledger (maintenance, utilities, salaries, stationery, labs); unified transaction history with CSV export; and daily cash Day-Book & monthly income/expenditure statements. Completed & fully tested.
- [x] **P7 Communication & Records**: Academic year event calendar with holiday tracking and month navigation; targeted announcements & circulars (campus-wide, class-specific, staff-only, parent-directed) with pinning; PTM scheduling console with date/time, venue, and agenda; two-way parent inquiry and teacher notes inbox with threaded replies; official certificate generator for Bonafide and Tamil Nadu State Board standard 16-field Transfer Certificates (TC) with sequential academic-year numbering (`SCH/26-27/BON-XXXXX` and `SCH/26-27/TC-XXXXX`) and printable DomPDF templates; and Student & Faculty CR-80 identity cards center with barcode rendering, blood groups, emergency contacts, and 8-per-page A4 print sheets (`print_students` and `print_staff`). Completed & fully tested.
- [x] **P8 Settings & Safety**: Comprehensive school profile management (board affiliation, currency, receipt sequence prefixes, biometric API secrets); academic year registry with active session switcher and historical archiving; role & permission security matrix viewer; immutable audit trail (`audit_logs`) with action/user filters and JSON state inspectors; entity CSV data exporters & one-click full MySQL database snapshot download (`.sql`); and administrative Mobile App Integration switch (`app_integration_enabled`) guarding Laravel Sanctum `/api/v1` REST endpoints (Token authentication, Student 360 profile, attendance logs, fee installments & dues, class timetable, homework board, and circulars) documented in `docs/API.md`. Completed & fully tested.
- [ ] **P9 Final QA**: Comprehensive test suite, browser click-through, performance tuning, hosting guide in README.md.
