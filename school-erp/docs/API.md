# Test Subject HSS ERP - Mobile & REST API v1 Specification

## Overview
This document specifies the REST API endpoints provided by the **Test Subject Higher Secondary School ERP** backend (Laravel 11/12 on PHP 8.2+ with MySQL 8.0).

These endpoints are designed for integration with mobile clients (e.g. Expo React Native for iOS & Android) and biometric hardware terminals.

- **Base URL**: `http://127.0.0.1:8000/api/v1`
- **Default Format**: `application/json`
- **Authentication**: Laravel Sanctum Bearer Token (`Authorization: Bearer <token>`)

---

## Safety Switch & API Gateway Guard
All mobile client API endpoints are guarded by the `EnsureApiEnabled` middleware, which checks the administrative flag `app_integration_enabled`.

- When **Disabled** (`app_integration_enabled = false`):
  All mobile endpoints return **HTTP 503 Service Unavailable**:
  ```json
  {
    "status": "error",
    "code": "API_INTEGRATION_DISABLED",
    "message": "Mobile App API Integration is currently disabled by system administrator. Enable it from Admin Settings."
  }
  ```
- When **Enabled** (`app_integration_enabled = true`):
  Endpoints function normally with Sanctum token authentication.

*(Note: The `/ping` health check and `/biometric/punch` terminal punch interface remain continuously operational).*

---

## 1. System Status & Health

### `GET /api/v1/ping`
Returns API service status, institutional header, and current integration state.

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "message": "School ERP API v1 is active",
  "school": "Test Subject Higher Secondary School",
  "app_integration_enabled": true,
  "timestamp": "2026-10-30T16:30:00+05:30"
}
```

---

## 2. Authentication Endpoints

### `POST /api/v1/auth/token`
Authenticate user credentials and issue a Sanctum personal access token.

**Request**:
```json
{
  "email": "parent@school.test",
  "password": "Password@123",
  "device_name": "iPhone 15 Pro (Expo)"
}
```

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "message": "Authentication successful.",
  "token": "1|qX8zO9mK2L...plainTextToken...",
  "token_type": "Bearer",
  "user": {
    "id": 5,
    "name": "P. Kalyanasundaram",
    "email": "parent@school.test",
    "role": "parent",
    "phone": "+91 98401 23456"
  }
}
```

---

### `GET /api/v1/auth/user`
**Headers**: `Authorization: Bearer <token>`
Returns the authenticated user's profile and linked student / staff dossiers.

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": {
    "id": 5,
    "name": "P. Kalyanasundaram",
    "email": "parent@school.test",
    "role": "parent",
    "phone": "+91 98401 23456",
    "student_profile": {
      "id": 1,
      "admission_number": "ADM-2026-0001",
      "first_name": "Aadhiran",
      "last_name": "Kalyanasundaram",
      "school_class_id": 1,
      "section_id": 1
    }
  }
}
```

---

### `POST /api/v1/auth/revoke`
**Headers**: `Authorization: Bearer <token>`
Revokes the current access token and logs out the device.

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "message": "Token revoked successfully. Logged out from device."
}
```

---

## 3. Student & Parent Portal Endpoints

### `GET /api/v1/student/profile`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "admission_number": "ADM-2026-0001",
    "roll_number": "1001",
    "first_name": "Aadhiran",
    "last_name": "Kalyanasundaram",
    "full_name": "Aadhiran Kalyanasundaram",
    "gender": "male",
    "date_of_birth": "2022-05-14",
    "blood_group": "O+",
    "class": "LKG",
    "section": "A",
    "academic_year": "2026-2027",
    "status": "active",
    "parent": {
      "name": "P. Kalyanasundaram",
      "phone": "+91 98401 23456",
      "email": "parent@school.test",
      "address": "42/7, West Main Street, Pudukkottai - 622 001"
    }
  }
}
```

---

### `GET /api/v1/student/attendance`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": {
    "student_id": 1,
    "student_name": "Aadhiran Kalyanasundaram",
    "summary": {
      "total_working_days": 45,
      "present_days": 43,
      "absent_days": 2,
      "attendance_percentage": 95.6
    },
    "recent_logs": [
      {
        "date": "2026-10-30",
        "status": "present",
        "remarks": null
      }
    ]
  }
}
```

---

### `GET /api/v1/student/fees`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": {
    "student_id": 1,
    "student_name": "Aadhiran Kalyanasundaram",
    "overview": {
      "total_assessed": 35000.0,
      "total_paid": 20000.0,
      "outstanding_balance": 15000.0,
      "currency": "₹"
    },
    "installments": [
      {
        "id": 1,
        "title": "Term 1 Tuition & Composite Fee",
        "term": "Term 1",
        "gross_amount": 15000.0,
        "concession_discount": 0.0,
        "net_payable": 15000.0,
        "amount_paid": 15000.0,
        "due_balance": 0.0,
        "due_date": "2026-06-30",
        "status": "paid"
      },
      {
        "id": 2,
        "title": "Term 2 Tuition & Activity Fee",
        "term": "Term 2",
        "gross_amount": 10000.0,
        "concession_discount": 0.0,
        "net_payable": 10000.0,
        "amount_paid": 5000.0,
        "due_balance": 5000.0,
        "due_date": "2026-10-31",
        "status": "partial"
      }
    ],
    "recent_receipts": [
      {
        "id": 1,
        "receipt_no": "SCH/2026-27/REC-00001",
        "receipt_date": "2026-06-15",
        "amount_paid": 15000.0,
        "payment_mode": "online",
        "transaction_ref": "UPI-9842100-TEST"
      }
    ]
  }
}
```

---

### `GET /api/v1/student/timetable`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": {
    "class": "Class 10",
    "section": "A",
    "schedule": {
      "Monday": [
        {
          "period": 1,
          "start_time": "09:00",
          "end_time": "09:45",
          "subject": "Mathematics",
          "teacher": "S. Ramakrishnan",
          "room": "Room 112"
        }
      ]
    }
  }
}
```

---

### `GET /api/v1/student/homework`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Quadratic Equations Exercise 3.2",
      "description": "Solve problems 1 to 15 from Chapter 3 in State Board textbook.",
      "subject": "Mathematics",
      "teacher": "S. Ramakrishnan",
      "assigned_date": "2026-10-30",
      "due_date": "2026-11-01"
    }
  ]
}
```

---

### `GET /api/v1/announcements`
**Headers**: `Authorization: Bearer <token>`

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Term 1 Examinations Time Table & Preparation Guidelines",
      "content": "The Term 1 CCE written examinations will commence from September 15...",
      "category": "academic",
      "target_type": "all",
      "publish_date": "2026-10-30",
      "is_pinned": true,
      "author": "Prof. S. Meenakshi"
    }
  ]
}
```

---

## 4. Hardware Biometric Terminal Interface

### `POST /api/v1/biometric/punch`
Always open to hardware wall clocks and fingerprint scanners using the shared API secret.

**Headers**:
- `X-Biometric-Secret`: `BIO-SEC-9842-TEST-KEY`

**Request Body**:
```json
{
  "device_id": "BIO-DEVICE-GATE-01",
  "employee_code": "STF-2026-0001",
  "punch_time": "2026-10-30 08:24:15",
  "punch_type": "check_in"
}
```

**Response (HTTP 200)**:
```json
{
  "status": "success",
  "message": "Punch recorded successfully",
  "data": {
    "employee_code": "STF-2026-0001",
    "staff_name": "S. Ramakrishnan",
    "punch_type": "check_in",
    "punch_time": "08:24:15",
    "attendance_status": "present",
    "is_late": false
  }
}
```
