# SYSTEM ARCHITECTURE

## Overview
The School ERP application is engineered on Laravel following domain-driven service architecture and MVC patterns. Business logic is strictly isolated in dedicated Services, leaving controllers slim and views purely declarative.

## Core Layers
1. **Presentation Layer**:
   - Blade templates with reusable atomic components (`x-stat-card`, `x-data-table`, `x-modal`, `x-toast`, `x-button`, etc.).
   - Tailwind CSS with an Emerald & Warm Amber design system, dark mode, responsive layouts.
   - Alpine.js for lightweight client reactivity, modals, dropdowns, and polling timers.
   - Chart.js for data visualization.
2. **Controller & Routing Layer**:
   - Web routes segregated by module prefix and protected by `RoleMiddleware`.
   - REST API routes (`/api/v1/*`) secured by Laravel Sanctum Bearer tokens and guarded by `AppIntegrationCheck` middleware.
3. **Business Logic & Service Layer**:
   - `FeeService`: Installment calculation, concession application, late fee computation, sequential receipt generation.
   - `StoreService`: Inventory management, stock deduction, sale transaction recording, profit calculation.
   - `AttendanceService`: Daily student roll-calls, staff biometric punch processing, late mark tagging.
   - `LeaveService`: Quota allocation, leave balance deduction, approval cascades.
   - `PayrollService`: Attendance-based monthly salary generation, deductions, allowances.
   - `TimetableService`: Period allocation, room/teacher clash validation, teacher substitution heuristics.
   - `ReportService`: CCE mark aggregation, grading scale translation, official PDF report card composition.
4. **Data Persistence Layer**:
   - MySQL 8.0 with InnoDB engine, utf8mb4 charset, strict foreign keys, and indexes on frequent lookup columns.
   - Database transactions (`DB::transaction`) for all financial, inventory, and payroll modifications.
