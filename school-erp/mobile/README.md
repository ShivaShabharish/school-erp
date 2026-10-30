# Test Subject Higher Secondary School - Mobile ERP App

Cross-platform native mobile application built with **Expo SDK 52** and **React Native (TypeScript)** for parents, students, and faculty of **Test Subject Higher Secondary School** (Pudukkottai, Tamil Nadu).

---

## 📱 App Highlights & Architecture

- **School Profile**: Test Subject Higher Secondary School, Tamil Nadu State Board (Affiliation: `TN-EDU-2026-4821`).
- **Design System**: Deep Emerald (`#047857`) and Warm Amber (`#D97706`).
- **REST Backend Integration**: Seamless communication with the Laravel 11/12 REST API over `/api/v1/*` secured by Laravel Sanctum token authentication.
- **Dynamic Server Gateway Switcher**: In-app custom endpoint selector allowing switching between:
  - Public Cloudflare Tunnel: `https://calgary-dental-increase-promotions.trycloudflare.com/api/v1`
  - Local Dev Emulator / WiFi: `http://10.0.2.2:8000/api/v1` or `http://<your-lan-ip>:8000/api/v1`

---

## 🌟 Feature Modules & Screen Registry

| Module | Screen Path | Features & Functionality |
| :--- | :--- | :--- |
| **Authentication** | `src/screens/auth/LoginScreen.tsx` | Secure login, password toggle, quick 1-tap demo credentials fill (Parent, Student, Teacher, Admin), and server URL configuration modal. |
| **Splash & Re-auth** | `src/screens/auth/SplashScreen.tsx` | School crest, institutional branding, automatic bearer token validation against `/api/v1/auth/user`. |
| **Home Dashboard** | `src/screens/home/HomeScreen.tsx` | Ward dossier, live attendance % gauge, outstanding fee dues alert, rapid shortcuts (Timetable, Homework, Attendance, Fees), and urgent broadcast announcements ticker. |
| **Timetable** | `src/screens/academics/TimetableScreen.tsx` | 6-day (Monday to Saturday) period schedule, 8 periods/day, subject badges, faculty names, and classroom numbers. |
| **Homework Board** | `src/screens/academics/HomeworkScreen.tsx` | Daily homework tasks, assigned dates, due countdowns, subject tags, and teacher instructions. |
| **Turnout & Attendance**| `src/screens/attendance/AttendanceScreen.tsx`| Attendance score gauge (Present/Absent ratio), compliance rating, and reverse chronological 30-day roll-call record logs. |
| **Fees & Payments** | `src/screens/fees/FeesScreen.tsx` | Term 1, 2, 3 installment cards, gross tuition vs scholarship concessions, due balances, UPI pay modal, and historical cashier receipts list with receipt numbers. |
| **Campus Notices** | `src/screens/notices/NoticesScreen.tsx` | Institutional circulars with category filters (Urgent, Academic, Exams, Events, General), search bar, and pinned notice banners. |
| **Student ID & Profile**| `src/screens/profile/ProfileScreen.tsx` | CR-80 digital student identity pass with barcode, blood group, emergency contact with one-tap dialing, institutional metadata, and secure sign-out. |

---

## 🚀 Running the Mobile App Locally

### 1. Prerequisites
- Node.js 18+ installed on your computer.
- Physical smartphone with **Expo Go** installed:
  - [Expo Go on Google Play Store](https://play.google.com/store/apps/details?id=host.exp.exponent)
  - [Expo Go on Apple App Store](https://apps.apple.com/app/expo-go/id982107779)

### 2. Start the Development Server
From the root workspace directory, navigate to `school-erp/mobile`:

```powershell
Set-Location "school-erp/mobile"
npx expo start
```

### 3. Open on your Phone
1. A QR code will appear in your terminal.
2. Open the camera app on iOS or the **Expo Go** app on Android.
3. Scan the QR code to load the app directly onto your physical phone over WiFi or tunnel.

---

## 🔑 Demo Credentials (1-Tap Login)

The app comes with a built-in Quick-Fill demo bar:

| Role | Email Address | Password | Student Dossier |
| :--- | :--- | :--- | :--- |
| **Parent** | `parent@school.test` | `Password@123` | Selvam Sundaram (Father of Aadhiran Selvam, Class 10-A) |
| **Student** | `student@school.test` | `Password@123` | Aadhiran Selvam (Class 10-A, ADM-2026-0001) |
| **Teacher** | `teacher@school.test` | `Password@123` | Faculty Staff Member |
| **Admin** | `admin@school.test` | `Password@123` | Institutional Administrator |

---

## 📦 Building Standalone APK / Production Bundle

To generate a standalone `.apk` for Android distribution without needing Expo Go:

```bash
# 1. Install EAS CLI globally
npm install -g eas-cli

# 2. Log in to your Expo account
eas login

# 3. Configure EAS Build
eas build:configure

# 4. Build Android APK for direct installation
eas build -p android --profile preview
```

The resulting `.apk` can be installed on any Android phone directly.
