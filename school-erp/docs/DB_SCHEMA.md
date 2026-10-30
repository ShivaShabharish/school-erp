# DATABASE SCHEMA SPECIFICATION

Database: `school_erp` (MySQL 8.0, utf8mb4_unicode_ci)

## Tables & Entity Relationships

### 1. Core & Authentication
- `users`: `id`, `name`, `email` (unique), `password`, `role` (enum: chairman, principal, accountant, teacher, staff, parent), `phone`, `avatar`, `is_active`, `remember_token`, timestamps.
- `audit_logs`: `id`, `user_id` (FK), `action`, `model_type`, `model_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, timestamps.
- `settings`: `id`, `key` (unique), `value` (text), `group`, timestamps.

### 2. Academic Entities
- `academic_years`: `id`, `name` (e.g., '2026-2027'), `start_date`, `end_date`, `is_current` (boolean), timestamps.
- `school_classes`: `id`, `name` (e.g. 'LKG', 'UKG', '1'..'12'), `order_sequence`, timestamps.
- `sections`: `id`, `class_id` (FK), `name` (e.g. 'A', 'B'), `room_no`, `max_capacity`, timestamps.
- `subjects`: `id`, `name`, `code`, `type` (core, elective, language), timestamps.
- `students`: `id`, `admission_no` (unique), `first_name`, `last_name`, `gender`, `dob`, `class_id` (FK), `section_id` (FK), `parent_id` (FK users), `roll_number`, `admission_date`, `status` (active, alumni, transferred), `blood_group`, `emergency_contact`, timestamps, softDeletes.
- `student_attendances`: `id`, `student_id` (FK), `date`, `status` (present, absent, late, half_day), `remarks`, timestamps.

### 3. Staff & HR
- `staff`: `id`, `user_id` (FK), `employee_code` (unique), `designation`, `department`, `qualification`, `joining_date`, `basic_salary`, `status`, timestamps.
- `staff_attendances`: `id`, `staff_id` (FK), `date`, `punch_in_time`, `punch_out_time`, `status` (present, absent, late, on_leave), `device_id`, timestamps.
- `leave_types`: `id`, `name` (Casual, Sick, Earned, Maternity), `annual_quota`, timestamps.
- `staff_leaves`: `id`, `staff_id` (FK), `leave_type_id` (FK), `start_date`, `end_date`, `days_count`, `reason`, `status` (pending, approved, rejected), `approved_by` (FK), timestamps.
- `payrolls`: `id`, `staff_id` (FK), `month`, `year`, `basic_salary`, `allowances`, `deductions`, `net_salary`, `status` (pending, paid), `paid_at`, timestamps.

### 4. Accounts & Billing
- `fee_structures`: `id`, `academic_year_id` (FK), `class_id` (FK), `term` (Term 1, Term 2, Term 3), `tuition_fee`, `term_fee`, `computer_lab_fee`, `library_fee`, `total_amount`, `due_date`, timestamps.
- `student_fee_dues`: `id`, `student_id` (FK), `fee_structure_id` (FK), `gross_amount`, `concession_amount`, `late_fine`, `net_amount`, `paid_amount`, `balance_amount`, `status` (unpaid, partial, paid), timestamps.
- `fee_receipts`: `id`, `receipt_no` (unique, e.g. `SCH/2026-27/REC-00001`), `student_fee_due_id` (FK), `student_id` (FK), `amount_paid`, `payment_method` (Cash, UPI, Cheque, Bank Transfer), `transaction_reference`, `collected_by` (FK), `notes`, `receipt_date`, timestamps.
- `expenses`: `id`, `category` (Salaries, Maintenance, Utilities, Stationery, Transport, Events), `title`, `vendor_name`, `bill_no`, `amount`, `payment_date`, `payment_method`, `approved_by` (FK), `notes`, timestamps.
- `store_items`: `id`, `name`, `category` (Uniform, Book, Notebook, Bag, Stationery), `item_code` (unique), `purchase_price`, `selling_price`, `stock_quantity`, `alert_threshold`, timestamps.
- `store_sales`: `id`, `receipt_no` (unique, e.g. `SCH/2026-27/STR-00001`), `student_id` (FK null), `customer_name`, `total_amount`, `payment_method`, `sold_by` (FK), `sale_date`, timestamps.
- `store_sale_items`: `id`, `store_sale_id` (FK), `store_item_id` (FK), `quantity`, `unit_price`, `subtotal`, timestamps.

### 5. Academics, Timetable & Communication
- `timetables`: `id`, `class_id` (FK), `section_id` (FK), `subject_id` (FK), `teacher_id` (FK staff), `day_of_week` (Monday..Saturday), `period_number`, `start_time`, `end_time`, `room_number`, timestamps.
- `exams`: `id`, `academic_year_id` (FK), `name` (Quarterly, Half-Yearly, Annual), `start_date`, `end_date`, timestamps.
- `exam_marks`: `id`, `exam_id` (FK), `student_id` (FK), `subject_id` (FK), `max_marks`, `marks_obtained`, `grade`, `remarks`, timestamps.
- `announcements`: `id`, `title`, `content`, `target_audience` (all, staff, parents, students), `class_id` (FK null), `posted_by` (FK), timestamps.
