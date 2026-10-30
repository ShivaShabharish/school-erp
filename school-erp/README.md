# Test Subject Higher Secondary School - Enterprise ERP System

A production-grade, full-stack School Enterprise Resource Planning (ERP) platform built with **Laravel 11**, **MySQL 8.0**, **Tailwind CSS**, **Alpine.js**, **Chart.js**, and **Laravel DomPDF**.

Designed specifically for **Test Subject Higher Secondary School** (Pudukkottai, Tamil Nadu), fully aligned with the **Tamil Nadu State Board Continuous and Comprehensive Evaluation (CCE)** examination and curriculum pattern.

---

## 🏛️ Institution Profile & Configuration

- **Institution Name**: Test Subject Higher Secondary School
- **Board Affiliation**: Tamil Nadu State Board of School Education (Continuous & Comprehensive Evaluation - CCE)
- **Affiliation / Recognition No**: `TN-EDU-2026-4821`
- **Location**: Main Road, Alangudi Gate, Pudukkottai - 622 001, Tamil Nadu, India
- **Classes**: Lower Kindergarten (LKG) to 12th Standard across Sections A & B (28 Class Sections)
- **Currency**: Indian Rupee (`₹` / INR)
- **Academic Year**: April to March (Current Session: `2026-2027`)
- **Sequential Billing Identifiers**:
  - Fee Receipts: `SCH/2026-27/REC-XXXXX`
  - School Store POS Bills: `SCH/2026-27/STR-XXXXX`
  - Bonafide Certificates: `SCH/26-27/BON-XXXXX`
  - Transfer Certificates (TC): `SCH/26-27/TC-XXXXX`
- **Design System Palette**: Deep Emerald (`#047857`) & Warm Amber (`#D97706`) with light/dark theme modes and AA contrast compliance.

---

## 🚀 Complete Feature Catalog

### 1. Multi-Role Authentication & Access Control
- Distinct permission hierarchies for **Chairman**, **Principal**, **Accountant**, **Teacher**, **Support Staff**, and **Parent**.
- Role-based redirect routing and strict authorization middleware isolation.

### 2. Admin Executive Dashboard
- 5 live metric tiles: Students Present (with live heartbeat ping), Staff On-Duty, Staff on Leave, Today's Fee Collections, and Monthly Net Operating Margin.
- 10-second lightweight JSON polling (`/admin/metrics/live`) for real-time operations.
- 28-section class turnout table with instant client search and filter.
- 7-day attendance trend analytics powered by Chart.js.
- Actionable operational alerts: late staff arrivals, fee defaulters, and low store inventory.

### 3. Students & Admissions Lifecycle
- Prospective admissions enquiry pipeline with status tracking and 1-click student conversion modal.
- 360° Student Dossier: biodata, verified document uploads, fee receipts history, academic marks, and progression logs.
- Batch Class-to-Class Promotion Wizard with prerequisite validations and audit trail.
- Class overview directory and section rosters with real-time room occupancies.
- Streamed UTF-8 CSV exports for pupil records.

### 4. Staff, Biometrics & Payroll
- Complete faculty and administrative staff directory with 360° personnel dossiers.
- Hardware Biometric Machine REST API: `POST /api/v1/biometric/punch` with secret key verification.
- Interactive Diagnostic Biometric Simulator Console for testing punch-in/out scenarios.
- Strict punctuality tracking with 08:30 AM on-time vs late cutoff threshold.
- Leave management engine with annual quota allocations (CL, ML, EL) and approval workflow.
- 7th Central Pay Commission (CPC) compliant monthly payroll generator with downloadable DomPDF payslips.

### 5. Academics, Timetables & CCE Examinations
- 6-day x 8-period weekly timetable matrix with automated teacher and room collision detection.
- Smart substitution recommendation engine cross-referencing daily biometric punches.
- Tamil Nadu State Board CCE examination planner: Formative Assessment (FA-A, FA-B), Summative Assessment (SA), and A1-E grade computations.
- Tabular spreadsheet-style marks entry sheet.
- Downloadable DomPDF CCE Progress Report Card.
- Coursework homework board and daily section roll-call register.

### 6. Dedicated Accounts & Billing Division
- Persistent top-right navigation header button for instant cashier access.
- Class-wise 3-term fee installment structures (Term 1, Term 2, Term 3) for LKG to 12th standard.
- Cashier collection desk with live student search, installment dues breakdown, and two-part receipts (Student Copy + Office Counterfoil).
- Institutional concessions & scholarship grants (Sibling 25%, Staff Ward, Merit ₹10,000, RTE).
- School Store POS retail register with barcode item scanning, atomic inventory deduction, and gross profit tracking.
- Double-entry expense voucher ledger.
- Daily cash Day-Book and monthly income vs expenditure surplus statements with CSV export.

### 7. Communication & Records Center
- Academic event calendar with month navigation and official Tamil Nadu public holidays (Pongal, Deepavali, Gandhi Jayanti, Independence Day).
- Broadcast circulars with pinning and targeted audience filtering (Campus-wide, Class-specific, Staff-only).
- Parent-Teacher Meetings (PTM) scheduling console with venue and agenda management.
- Direct two-way parent inquiries and teacher notes inbox with threaded replies.
- Official certificate generation:
  - **Bonafide Certificate** with school crest, purpose statement, seal box, and principal signature.
  - **Transfer Certificate (TC)** with Tamil Nadu State Board official 16-field format.
- Student & Staff CR-80 Identity Cards center with barcodes, emergency contacts, blood groups, and 8-per-page A4 print sheets.

### 8. Settings, Safety & REST API Gateway
- Institution profile editor, currency symbol, and receipt sequence configurator.
- Academic year session registry with 1-click active session switcher.
- Immutable security audit trail (`audit_logs`) with user/action filters and JSON state inspector.
- One-click MySQL database snapshot generator (`.sql`) and CSV data archival.
- Administrative **Mobile App Integration Switch** (`app_integration_enabled`) guarding Laravel Sanctum `/api/v1` REST endpoints for future mobile app deployment.
- Complete API specification documented in `docs/API.md`.

---

## 🔑 Demo Login Credentials

All demo accounts share the standard password: **`Password@123`**

| Role | Email | Portal / Destination | Scope of Access |
| :--- | :--- | :--- | :--- |
| **Chairman** | `chairman@school.test` | `/admin/dashboard` | Sovereign read/write access across all divisions |
| **Principal** | `principal@school.test` | `/admin/dashboard` | Academic, faculty, timetable & student administration |
| **Accountant** | `accountant@school.test` | `/accounts/dashboard` | Cashier desk, fee collections, store POS & expense books |
| **Teacher** | `teacher@school.test` | `/staff/dashboard` | Assigned timetable, CCE marks entry & daily roll-call |
| **Staff** | `murugan@school.test` | `/staff/dashboard` | Personal biometric punches, leave apply & payslips |
| **Parent** | `parent@school.test` | `/parent/dashboard` | Ward 360 profile, fee receipts, results & notes inbox |

---

## 💻 Local Quickstart (Windows & Linux)

### System Prerequisites
- **PHP**: 8.2 or higher with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`, `intl`
- **MySQL**: 8.0 or MariaDB 10.4+
- **Composer**: 2.x
- **Node.js**: 18.x or 20.x LTS with NPM

### 1. Clone & Navigate
```bash
git clone <repository-url>
cd school-erp/web
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
Copy `.env.example` to `.env` and configure your MySQL credentials:
```ini
APP_NAME="Test Subject HSS ERP"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=school_erp_user
DB_PASSWORD=SchoolErp@2026
```

### 4. Database Setup & Realistic Seeding
Run migrations and load over 300 students, faculty, timetables, and billing structures:
```bash
php artisan migrate:fresh --seed
```

### 5. Build Assets & Start Development Server
```bash
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```
Open your browser at **`http://127.0.0.1:8000`**

Health check endpoint: **`http://127.0.0.1:8000/health`**

---

## 🧪 Automated Test Suite Execution

The application includes an extensive feature test suite covering all operational modules:
```bash
cd school-erp/web
php artisan test
```
**Current Status**: **66 tests passed, 314 assertions, 0 failures (100% pass rate)**.

---

## 🌐 Production Hosting & Deployment Guide

This guide outlines the standard production deployment procedure on an Ubuntu 22.04 / 24.04 LTS server, Apache, Nginx, or cPanel environment.

---

### Option A: Deployment on Ubuntu with Nginx & PHP 8.2-FPM (Recommended)

#### 1. Install System Packages
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip php8.2-intl \
    git curl unzip supervisor
```

#### 2. Deploy Code to Web Root
```bash
sudo mkdir -p /var/www/school-erp
cd /var/www/school-erp
sudo git clone <repository-url> .
cd web
sudo composer install --no-dev --optimize-autoloader
sudo npm ci && sudo npm run build
```

#### 3. Set Proper Directory Permissions
The web server user (`www-data`) requires ownership of storage and bootstrap cache:
```bash
sudo chown -R www-data:www-data /var/www/school-erp/web/storage
sudo chown -R www-data:www-data /var/www/school-erp/web/bootstrap/cache
sudo chmod -R 775 /var/www/school-erp/web/storage
sudo chmod -R 775 /var/www/school-erp/web/bootstrap/cache
```

#### 4. Configure Production Environment
Edit `/var/www/school-erp/web/.env`:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://school.yourdomain.edu.in

# Database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp_prod
DB_USERNAME=erp_admin
DB_PASSWORD=StrongProductionPassword@2026

# Production caching drivers
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```
Generate encryption key and run production migrations:
```bash
php artisan key:generate --force
php artisan migrate --force --seed
```

#### 5. Configure Nginx Server Block
Create `/etc/nginx/sites-available/school-erp.conf`:
```nginx
server {
    listen 80;
    server_name school.yourdomain.edu.in;
    root /var/www/school-erp/web/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 32M;
}
```
Enable site and test configuration:
```bash
sudo ln -s /etc/nginx/sites-available/school-erp.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 6. Enable Free SSL via Certbot (HTTPS)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d school.yourdomain.edu.in
```

---

### Option B: Deployment on Apache 2.4

Enable Apache rewrite module and configure VirtualHost:
```bash
sudo a2enmod rewrite ssl headers
```
Create `/etc/apache2/sites-available/school-erp.conf`:
```apache
<VirtualHost *:80>
    ServerName school.yourdomain.edu.in
    DocumentRoot /var/www/school-erp/web/public

    <Directory /var/www/school-erp/web/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/school_erp_error.log
    CustomLog ${APACHE_LOG_DIR}/school_erp_access.log combined
</VirtualHost>
```
Enable site and restart Apache:
```bash
sudo a2ensite school-erp.conf
sudo systemctl restart apache2
```

---

### Option C: Deployment on cPanel / Shared Hosting

1. **Upload Files**: Upload the contents of the `school-erp/web` folder to your cPanel home directory (e.g. `/home/username/school-erp`).
2. **Web Root Configuration**:
   - In cPanel **Domains / Subdomains**, set the Document Root directly to `/home/username/school-erp/public`.
   - Alternatively, if deploying to `public_html`, move the contents of `web/public` into `public_html` and the remaining Laravel directories into `/home/username/school-erp`, then update `public_html/index.php` paths:
     ```php
     require __DIR__.'/../school-erp/vendor/autoload.php';
     $app = require_once __DIR__.'/../school-erp/bootstrap/app.php';
     ```
3. **Database Setup**:
   - Create a MySQL database and user via cPanel **MySQL Databases**.
   - Grant `ALL PRIVILEGES` to the user on the database.
   - Update `.env` in the root with the generated DB name and password.
4. **Run Migrations**: Run migrations via cPanel Terminal or import the seed snapshot via **phpMyAdmin**.

---

### ⏱️ Production Task Scheduler & Cron Configuration

Laravel's scheduler automates attendance cutoff calculations, daily fee reminders, and database maintenance.

Add the following single cron entry to your server (`crontab -e`):
```bash
* * * * * cd /var/www/school-erp/web && php artisan schedule:run >> /dev/null 2>&1
```

On cPanel:
Navigate to **Cron Jobs** &rarr; Select "Once Per Minute (`* * * * *`)" &rarr; Enter:
```text
/usr/local/bin/php /home/username/school-erp/artisan schedule:run >> /dev/null 2>&1
```

---

### ⚡ Production Caching & Speed Optimization

Before going live, compile all route definitions, views, and configuration into static cache files for sub-50ms response times:
```bash
cd /var/www/school-erp/web
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To refresh caches after code updates:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 💾 Database Backup & Disaster Recovery

#### Automated Daily MySQL Backup via Cron
```bash
sudo mkdir -p /var/backups/school_erp
0 2 * * * mysqldump -u erp_admin -p'StrongProductionPassword@2026' school_erp_prod | gzip > /var/backups/school_erp/erp_backup_$(date +\%F).sql.gz
```

#### One-Click Web UI Backup
Administrative users can navigate to **Settings & Safety &rarr; Data Export & Archival &rarr; Download Database .SQL Dump** to download an on-demand schema and data snapshot.

#### Restore Procedure
```bash
gunzip < /var/backups/school_erp/erp_backup_2026-10-30.sql.gz | mysql -u erp_admin -p school_erp_prod
```

---

## 📱 Mobile App (Expo) Integration

The school ERP includes ready-to-use **Laravel Sanctum REST API endpoints** under `/api/v1/` for integration with an Expo React Native mobile application for parents, students, and teachers.

- See [docs/API.md](file:///c:/Users/DELL/OneDrive/Desktop/Scl%20project%203.0/school-erp/docs/API.md) for full endpoint specifications, authentication flows, and request/response payloads.
- Toggle API access on or off via the **Settings &rarr; Mobile API Gateway** toggle switch.

---

## 📄 License & Attribution

Developed for **Test Subject Higher Secondary School**, Pudukkottai, Tamil Nadu. Built on Laravel, Tailwind CSS, Alpine.js, Chart.js, and DomPDF.
