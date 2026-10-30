# REST API SPECIFICATION (/api/v1)

## Authentication & Security
- Guarded by Laravel Sanctum Bearer tokens (`Authorization: Bearer <token>`).
- Controlled globally by the setting `app_integration_enabled` (toggleable from System Settings).
- When `app_integration_enabled = false`, all mobile API routes return `403 Forbidden` with `{ "message": "Mobile API integration is currently disabled by administrator." }`.

---

## Biometric Machine Punch Integration
### POST `/api/v1/biometric/punch`
Simulates or receives real hardware biometric attendance punches.

- **Headers**:
  - `Content-Type: application/json`
  - `X-Biometric-Secret: <BIOMETRIC_API_SECRET>`
- **Payload**:
  ```json
  {
    "device_id": "BIO-GATE-01",
    "staff_id": 4,
    "timestamp": "2026-10-30 08:24:15"
  }
  ```
- **Response**: `200 OK`
  ```json
  {
    "status": "success",
    "message": "Punch recorded successfully",
    "data": {
      "attendance_id": 142,
      "staff_name": "R. Santhosh Kumar",
      "punch_time": "08:24:15",
      "state": "on-time"
    }
  }
  ```

---

## Mobile App Endpoints (Expo Future App)

### POST `/api/v1/auth/login`
- **Request**: `{ "email": "parent@school.test", "password": "Password@123" }`
- **Response**: `{ "token": "1|sanctum...", "user": { ... }, "role": "parent" }`

### GET `/api/v1/parent/ward-summary`
- **Response**: Details of student ward, daily attendance percentage, pending fee dues, announcements, and recent exam report card.

### GET `/api/v1/teacher/today-schedule`
- **Response**: Today's timetable slots, period numbers, subjects, and section assignments.
