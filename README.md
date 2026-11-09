# 🏥 Medical Points Management System (MPMS)
### A Modern, Multi-Center Clinical Management & Points-Based Medicine Dispensation Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-^8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/TailwindCSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4" />
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7" />
  <img src="https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" alt="License" />
</p>

---

## 📑 Table of Contents

- [Overview](#-overview)
- [System Architecture & Workflow](#-system-architecture--workflow)
- [Key Features & Technical Highlights](#-key-features--technical-highlights)
- [Role-Based Functional Breakdown](#-role-based-functional-breakdown)
  - [1. Super Administrator](#1-super-administrator-superadmin)
  - [2. Medical Center Manager](#2-medical-center-manager-centermanager)
  - [3. Doctor / Physician](#3-doctor--physician-doctor)
  - [4. Pharmacist](#4-pharmacist-pharmacist)
  - [5. Receptionist](#5-receptionist-reception)
  - [6. Patient](#6-patient-patient)
- [Database Schema & Entity Relationships](#-database-schema--entity-relationships)
- [Smart Arabic Normalization Engine](#-smart-arabic-normalization-engine)
- [Modular Routing Architecture](#-modular-routing-architecture)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation & Setup Guide](#-installation--setup-guide)
- [Default Demo Credentials](#-default-demo-credentials)
- [Automated Testing Suite](#-automated-testing-suite)
- [Security & Concurrency Protections](#-security--concurrency-protections)
- [License](#-license)

---

## 📌 Overview

**Medical Points Management System (MPMS)** is an enterprise-grade medical and clinic management system engineered with **Laravel 12**, **Tailwind CSS v4**, and **Vite**. The platform introduces a **Medical Points Subsidization and Rationing Engine**, allowing health organizations, medical relief points, and distributed healthcare centers to manage patient quotas, prevent pharmaceutical arbitrage, track clinic visits, and ensure equitable medicine distribution.

The system natively supports full **Right-to-Left (RTL)** presentation, multi-center operations, real-time triage queueing, bidirectional Arabic/English pharmaceutical search, and Arabic-shaped PDF reporting.

---

## 🔄 System Architecture & Workflow

The platform coordinates an end-to-end clinical workflow connecting reception desks, consultation rooms, and central pharmacy dispensaries:

```
[ Patient Arrives ]
        │
        ▼
[ Reception Desk ] ────► (National ID Search via AJAX / New Patient Onboarding)
        │
        ├──────────────► (Triage Priority: Normal vs. Emergency)
        ▼
[ Live Waiting Queue ]
        │
        ▼
[ Doctor's Clinic ] ───► (Start Consultation -> Diagnose & Review History)
        │
        ├──────────────► (Generate Electronic Prescription)
        ▼
[ Doctor Completes Visit ]
        │
        ▼
[ Pharmacy Dispensary ]
        │
        ├──────────────► 1. Verifies completed doctor visit for today
        ├──────────────► 2. Locks inventory row (`lockForUpdate`) & checks stock
        ├──────────────► 3. Validates patient's remaining points >= total medicine cost
        │
        ▼
[ Atomic Transaction ] ──► (Deduct Stock + Deduct Points + Mark Dispensed + Log Audit)
        │
        ▼
[ Patient Receives Medication ]
```

---

## 🌟 Key Features & Technical Highlights

### 1. Point-Based Medicine Quota & Dispensation Engine
- **Fair Allocation:** Each patient is granted a configurable balance of medical points upon registration.
- **Dynamic Pricing in Points:** Every pharmaceutical item in the master catalog carries an assigned `points_cost`.
- **Pre-Dispense Verification Guard:** Medications can only be dispensed if the patient has completed a medical consultation in that center on the same day (preventing unauthorized prescription fills).
- **Atomic Concurrency Handling:** Utilizes `lockForUpdate()` database row locks and database transactions (`DB::beginTransaction()`, `commit()`, `rollBack()`) to eliminate race conditions, double-spending, and negative stock quantities.

### 2. Intelligent Arabic Text Normalization (`SearchableArabic` Trait)
- Standard database queries fail when searching Arabic text due to typographical variations (e.g., `أ / إ / آ / ا`, `ة / ه`, `ى / ي`).
- MPMS includes a custom Eloquent trait that executes nested SQL `REPLACE()` functions at runtime:
  - Normalizes `[أ, إ, آ]` to `ا`
  - Normalizes `[ة]` to `ه`
  - Normalizes `[ى]` to `ي`
- Simultaneously performs case-insensitive wildcard searches against the English pharmaceutical catalog (`name_en`).

### 3. Native Arabic PDF Generation Engine
- Powered by `barryvdh/laravel-dompdf` integrated with `khaled.alshamaa/ar-php`.
- Performs glyph reshaping and bidirectional (BiDi) text reconstruction to ensure that complex Arabic ligatures, diacritics, and right-to-left layout constraints render properly in generated PDF reports.

### 4. Real-Time Patient Triage & Queue Management
- **Instant Patient Lookup:** Asynchronous AJAX queries by National ID (`national_id`) or name.
- **Priority Queuing:** High-urgency cases can be marked as `priority = 1 (Emergency)`, moving them ahead of standard appointments (`priority = 0 (Normal)`).
- **Visit States:** Granular tracking of states (`registered`, `waiting`, `in_progress`, `completed`, `cancelled`).

---

## 🎭 Role-Based Functional Breakdown

The application implements a strict **Role-Based Access Control (RBAC)** architecture backed by `RoleMiddleware`. Each role is encapsulated with its own dedicated route group, views, and controllers:

```
                                 ┌──────────────────────────────┐
                                 │   SuperAdmin (Full Access)   │
                                 └──────────────┬───────────────┘
                                                │
                 ┌──────────────────────────────┼──────────────────────────────┐
                 ▼                              ▼                              ▼
      ┌────────────────────┐         ┌────────────────────┐         ┌────────────────────┐
      │   CenterManager    │         │       Doctor       │         │     Pharmacist     │
      └──────────┬─────────┘         └──────────┬─────────┘         └──────────┬─────────┘
                 │                              │                              │
                 └──────────────────────────────┼──────────────────────────────┘
                                                ▼
                                     ┌────────────────────┐
                                     │     Reception      │
                                     └──────────┬─────────┘
                                                ▼
                                     ┌────────────────────┐
                                     │      Patient       │
                                     └────────────────────┘
```

### 1. 👑 Super Administrator (`SuperAdmin`)
- **Executive Dashboard:** Global metrics across all medical centers (active users, total inventory value, points consumed).
- **Medical Center Management:** Full CRUD operations on healthcare facilities (`name`, `location`, `status`).
- **User Account Provisioning:** Creation, role assignment, center association, and suspension of staff members.
- **Central Medicine Formulary:** Full CRUD operations on pharmaceutical items (Arabic name, English name, points cost, expiry dates).
- **Role & Permission Management:** Inspect and configure role definitions.
- **Global Analytical Reports:** Filterable analytics (by date range, center, or medicine) with export to Arabic PDF.

### 2. 🏢 Medical Center Manager (`CenterManager`)
- **Facility Dashboard:** Real-time visibility into local doctor visits, queued patients, and today's dispensations.
- **Center Inventory Management:** Direct monitoring and restocking of medicine inventory levels within the specific facility.
- **Staff Oversight:** Management and status review of medical and administrative staff assigned to the center.
- **Center Patients Registry:** Browsing of all registered patients, their medical files, and total visits.
- **Dispensation Audit Trail:** Detailed log of all prescription items fulfilled within the facility.
- **Center PDF Reports:** Customized performance and inventory depletion reports ready for export.

### 3. 🩺 Doctor / Physician (`Doctor`)
- **Clinical Dashboard:** Overview of waiting room volume, completed visits, and active consultations.
- **Today's Patient Queue:** Real-time list of patients awaiting examination, sorted by priority and arrival time.
- **Consultation Controls:**
  - `Enter`: Marks the patient visit as `in_progress`.
  - `Complete`: Finalizes consultation notes and allows subsequent medicine dispensing.
- **Electronic Prescription (E-Rx):**
  - Add multiple medication items with specific dosage instructions and quantity.
  - Interactive modal searching across available medicines.
- **Patient History:** Inspect previous consultations, diagnoses, and past prescription logs.

### 4. 💊 Pharmacist (`Pharmacist`)
- **Dispensary Dashboard:** Daily fulfillment count, points deducted, and critical stock notifications.
- **Prescription Search & Fulfillment:**
  - Retrieve patient prescriptions via National ID or visit record.
  - Review prescribed items, dosage, and doctor notes.
  - Automated check for a completed consultation on the same date.
  - One-click dispensation with automatic inventory and points deduction.
- **Manual / Direct Dispensation:** Ability to dispense emergency or over-the-counter medicine directly to a patient with verified identity and sufficient points.
- **Stock & Expiry Tracking:** Real-time view of current stock levels and upcoming expiration warnings.

### 5. 📋 Receptionist (`Reception`)
- **Patient Onboarding:** Register new patients with full demographics (Full Name, National ID, Phone, DOB, Address, and Initial Points Quota).
- **AJAX National ID Lookup:** Instant search to avoid duplicate records and quickly retrieve existing profiles.
- **Queue Dispatcher:** Assign patient to an on-duty physician, define priority level (Normal vs. Emergency), and queue the visit.
- **Queue Operations:** Reassign, monitor, or cancel waiting visits (`registered` / `waiting` / `cancelled`).
- **Patient Quota Adjustments:** Update patient contact details or modify points quota when approved.

### 6. 👤 Patient (`Patient`)
- **Personal Health Portal:** Real-time dashboard showing available and consumed medical points.
- **Pharmaceutical Directory:** Search and browse available medicines, details, and point costs.
- **Prescription Archive:** Complete history of issued electronic prescriptions and physician instructions.
- **Dispense Log:** Exact breakdown of dispensed medicines, dispensing center, timestamps, and points spent.

---

## 🗄️ Database Schema & Entity Relationships

The data model uses normalized relational structures with strict foreign key constraints:

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     roles       │       │ medical_centers │       │    medicines    │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id              │       │ id              │       │ id              │
│ name            │       │ name            │       │ name            │
└────────┬────────┘       │ address         │       │ name_en         │
         │                │ phone           │       │ points_cost     │
         │ 1              └────────┬────────┘       │ expiry_date     │
         │                         │                └────────┬────────┘
         ▼ *                       │ 1                       │ 1
┌─────────────────┐                │                         │
│      users      │◄───────────────┤                         │
├─────────────────┤                │                         │
│ id              │                │                         │
│ name, username  │                │                         │
│ email, password │                │                         │
│ role_id (FK)    │                │                         │
│ center_id (FK)  │                │                         │
└────────┬────────┘                │                         │
         │ 1                       │                         │
         ▼ *                       │                         │
┌─────────────────┐                │                         │
│    patients     │                │                         │
├─────────────────┤                │                         │
│ id              │                │                         │
│ user_id (FK)    │                │                         │
│ full_name       │                │                         │
│ national_id     │                │                         │
│ points          │                │                         │
└────────┬────────┘                │                         │
         │ 1                       │ 1                       │
         ├─────────────────────────┼─────────────────────────┤
         ▼ *                       ▼ *                       ▼ *
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     visits      │       │   inventories   │       │prescript._items │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id              │       │ id              │       │ id              │
│ patient_id (FK) │       │ center_id (FK)  │       │ prescript._id   │
│ doctor_id (FK)  │       │ medicine_id(FK) │       │ medicine_id(FK) │
│ center_id (FK)  │       │ quantity        │       │ dosage, quantity│
│ status          │       └─────────────────┘       │ is_dispensed    │
│ priority        │                                 └────────┬────────┘
└─────────────────┘                                          │ 1
                                                             ▼ *
                                                    ┌─────────────────┐
                                                    │    dispenses    │
                                                    ├─────────────────┤
                                                    │ id              │
                                                    │ item_id (FK)    │
                                                    │ pharmacist_id   │
                                                    │ center_id (FK)  │
                                                    │ points_used     │
                                                    └─────────────────┘
```

---

## 🔍 Smart Arabic Normalization Engine

Arabic text normalization is handled in [`app/Traits/SearchableArabic.php`](file:///c:/xampp/htdocs/MedicalPointsManagementSystem/app/Traits/SearchableArabic.php). When a search query is passed, the trait applies the following logic:

```php
// Normalize query input string
$search = ['أ', 'إ', 'آ', 'ة', 'ى'];
$replace = ['ا', 'ا', 'ا', 'ه', 'ي'];
$normalizedWord = str_replace($search, $replace, $word);

// Generate matching nested SQL replacement for Arabic columns:
REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`column`, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه'), 'ى', 'ي') LIKE '%query%'

// Case-insensitive query for English columns:
LOWER(`name_en`) LIKE '%query%'
```

This ensures that searching for `احمد`, `أحمد`, or `إحمد` will return the exact same patient or medicine records without database restructuring.

---

## 🗂️ Modular Routing Architecture

The routing system is split into role-specific sub-route modules loaded through [`routes/web.php`](file:///c:/xampp/htdocs/MedicalPointsManagementSystem/routes/web.php):

| Route File | Prefix | Role Middleware | Scope & Responsibilities |
| :--- | :--- | :--- | :--- |
| `routes/groups/auth.php` | `/` | Guest / Auth | Authentication, Login, Password Reset, Logout |
| `routes/groups/superadmin.php` | `/superadmin` | `RoleMiddleware:SuperAdmin` | Centers, Users, Medicines, Roles, Global Reports |
| `routes/groups/manager.php` | `/manager` | `RoleMiddleware:CenterManager` | Stock management, Center staff, Center reports |
| `routes/groups/doctor.php` | `/doctor` | `RoleMiddleware:Doctor` | Today's queue, Patient history, E-Prescriptions |
| `routes/groups/pharmacist.php` | `/pharmacist` | `RoleMiddleware:Pharmacist` | Prescription fulfillment, Manual dispense, Stock |
| `routes/groups/reception.php` | `/reception` | `RoleMiddleware:Reception` | Patient intake, Waiting queue, Triage dispatch |
| `routes/groups/patient.php` | `/patient` | `RoleMiddleware:Patient` | Personal points, Prescription records, Dispense logs |
| `routes/groups/shared.php` | `/*` | `auth` | Common personal and security profile updates |

---

## 💻 Tech Stack

| Layer | Technology | Version | Description |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | [Laravel](https://laravel.com/) | `12.x` | Modern PHP Web Application Framework |
| **Language Runtime** | [PHP](https://www.php.net/) | `^8.2` | Typed properties, attributes, match expressions |
| **Database** | MySQL / MariaDB | `8.x` / `10.x` | Relational storage with Foreign Keys & Transactions |
| **Frontend Framework** | [Tailwind CSS](https://tailwindcss.com/) | `v4.x` | Utility-first responsive styling with RTL support |
| **Frontend Bundler** | [Vite](https://vitejs.dev/) | `7.x` | Next-generation asset compilation & Hot Reload |
| **PDF Engine** | [DOMPDF](https://github.com/barryvdh/laravel-dompdf) | `^3.x` | Server-side HTML to PDF converter |
| **Arabic Text Shaping** | [Ar-PHP](https://github.com/khaled-alshamaa/ar-php) | `*` | Ligature and BiDi processor for Arabic PDFs |
| **Testing Harness** | [PHPUnit](https://phpunit.de/) | `11.5+` | Unit and Feature test framework |

---

## ⚙️ System Requirements

Ensure your environment satisfies the following prerequisites:
- **PHP:** `>= 8.2` (Extensions required: `OpenSSL`, `PDO`, `Mbstring`, `Tokenizer`, `XML`, `Ctype`, `JSON`, `BCMath`, `Fileinfo`, `GD`)
- **Composer:** `>= 2.2`
- **Node.js & NPM:** Node `>= 18.x`, NPM `>= 9.x`
- **Database Server:** MySQL `>= 8.0` or MariaDB `>= 10.4`
- **Web Server:** Apache / Nginx / XAMPP / Laragon

---

## 🚀 Installation & Setup Guide

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/MedicalPointsManagementSystem.git
cd MedicalPointsManagementSystem
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Configure the Environment
Create your local environment file:
```bash
cp .env.example .env
```
Open `.env` and set your database connection parameters:
```env
APP_NAME="Medical Points Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_points_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Database Migrations & Seeders
Execute the migrations and populate initial roles, medical centers, medicines, and sample user accounts:
```bash
php artisan migrate --seed
```

### 6. Install & Compile Frontend Assets
```bash
npm install
npm run build
```
*(For active frontend development with live hot-reloading, run `npm run dev` instead).*

### 7. Launch Local Development Server
```bash
php artisan serve
```
Open your browser and navigate to:
👉 **`http://127.0.0.1:8000`**

---

## 🔑 Default Demo Credentials

The database seeder automatically creates pre-configured accounts for all supported roles.

> **Note:** All seeded test accounts share the same default password: **`password`**

| Role | Username | Email Address | Password | Assigned Medical Center |
| :--- | :--- | :--- | :--- | :--- |
| **SuperAdmin** | `admin` | `admin@example.com` | `password` | *Global (All Centers)* |
| **CenterManager** | `manager1` | `manager1@example.com` | `password` | Center #1 (North Center) |
| **Doctor** | `doctor1` | `doctor1@example.com` | `password` | Center #1 (North Center) |
| **Doctor** | `doctor2` | `doctor2@example.com` | `password` | Center #1 (North Center) |
| **Pharmacist** | `pharmacist1` | `pharmacist1@example.com` | `password` | Center #1 (North Center) |
| **Receptionist** | `reception1` | `reception1@example.com` | `password` | Center #1 (North Center) |
| **Patient** | `patient1` | `ahmed.najjar@example.com` | `password` | *National ID: 401234567* |

---

## 🧪 Automated Testing Suite

The project includes unit and feature test coverage built with **PHPUnit 11** to guarantee data integrity around sensitive operations such as balance calculation, session handling, and user permissions.

### Test Coverage Highlights
- `tests/Feature/PatientPointsTest.php`:
  - Verifies receptionist registration of new patients with points quota.
  - Verifies modification, credit, and deduction of patient points.
  - Asserts database records and session notifications.
- `tests/Feature/LoginTest.php` & `LogoutTest.php`:
  - Verifies authentication flow, invalid credentials rejection, and role-based redirects.
- `tests/Feature/UserManagementTest.php`:
  - Verifies SuperAdmin user creation, authorization policies, and center association.

### Running the Test Suite
```bash
# Run all tests
php artisan test

# Run a specific feature test class
php artisan test --filter=PatientPointsTest

# Clear configuration cache before testing
composer test
```

---

## 🛡️ Security & Concurrency Protections

- **Role Guard Enforcement:** `RoleMiddleware` intercepts every incoming request and cross-validates authenticated user credentials against the required role.
- **Race Condition Immunity:** Pharmacist dispensations lock stock records using `lockForUpdate()`, safeguarding against simultaneous duplicate claims.
- **Visit Verification Shield:** Medicines cannot be dispensed without an authenticated, completed physician consultation within the same center on the same calendar day.
- **SQL Injection Prevention:** 100% parameter-bound queries via Eloquent ORM.
- **Cross-Site Request Forgery (CSRF):** All mutating requests (POST, PUT, DELETE) are guarded by CSRF token verification tokens.
- **Cryptographic Password Hashing:** Powered by standard Bcrypt (`Hash::make`).

---

## 📄 License

This software is released under the **[MIT License](LICENSE)**.
