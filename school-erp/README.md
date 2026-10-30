# Test Subject Higher Secondary School - Complete ERP System

A production-grade, full-stack School Enterprise Resource Planning (ERP) platform built with **Laravel 11**, **MySQL 8.0**, **Tailwind CSS**, **Alpine.js**, and **Chart.js**.

---

## Key Features
- **Design System**: Distinctive Deep Emerald (`#047857`) & Warm Amber (`#D97706`) palette with Plus Jakarta Sans & Inter typography, dark mode, and AA contrast compliance.
- **Dedicated Accounts & Billing Portal**: Accessed instantly via the top-right persistent button with full fee structure, installment tracking, concessions, double-entry expense ledger, and school store POS inventory.
- **Biometric Punch Integration**: REST API endpoint `POST /api/v1/biometric/punch` with real-time dashboard updates and demo simulator.
- **Academic Engine**: Timetable scheduling with room/teacher clash detection, automated absent teacher substitutions, Tamil Nadu State Board CCE examinations, and PDF report cards.
- **Reliable Data Persistence**: Full database transactions, indexed lookups, sequential receipts (`SCH/2026-27/REC-XXXXX`), and realistic seed data (~300 students, 25 staff, full ledger).

---

## Quick Start & Running the App

### Requirements
- PHP 8.2+ with `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`, `intl` extensions enabled.
- MySQL 8.0 running on `127.0.0.1:3306`.
- Node.js 18+ and Composer 2.x.

### Launch Application
Simply run the startup script:
```powershell
.\school-erp\scripts\start.ps1
```
The application will launch immediately at:
👉 **`http://127.0.0.1:8000`**

Health check endpoint:
👉 **`http://127.0.0.1:8000/health`**

---

## Demo Login Credentials
All demo accounts use the standard password: **`Password@123`**

| Role | Email | Access Scope |
| :--- | :--- | :--- |
| **Chairman** | `chairman@school.test` | Full Administrative & Executive Access |
| **Principal** | `principal@school.test` | Full Academic & Administrative Access |
| **Accountant** | `accountant@school.test` | Dedicated Accounts & Billing Portal Only |
| **Teacher** | `teacher@school.test` | Timetable, Class Roll-Call, CCE Marks Entry |
| **Parent** | `parent@school.test` | Ward Dashboard, Attendance, Fees & Results |

---

## Automated Database Reset & Seeding
To reset the database to a fresh state with realistic Indian school seed data:
```powershell
cd school-erp/web
php artisan migrate:fresh --seed
```

---

## Running the Automated Test Suite
```powershell
cd school-erp/web
php artisan test
```
