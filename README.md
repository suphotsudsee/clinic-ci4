# Clinic CI4 - ระบบคลินิกตรวจโรค

ระบบคลินิกบน CodeIgniter 4 รองรับลงทะเบียนผู้ป่วยจากบัตรประชาชน, บันทึกการตรวจ, ดูประวัติย้อนหลัง, รายงาน และ export พร้อม audit log และแนวทาง PDPA

## Phase 1: System Design & Blueprint

### 1) Architecture Diagram (Text)

```text
[Smart Card Reader Hardware]
        |
        v
[Local Card Reader Service (127.0.0.1:8888)]
        |
        v
[CodeIgniter 4 Web App] --- [Session/Auth Filters] --- [Role Control]
        |
        +--> [Patient Module]
        +--> [Visit/Medical Record Module]
        +--> [Report/Export Module]
        +--> [Audit Log Module]
        |
        v
[MySQL/MariaDB]
```

### 2) Folder Structure

```text
clinic-ci4/
 ├─ app/
 │   ├─ Controllers/
 │   ├─ Models/
 │   ├─ Views/
 │   ├─ Filters/
 │   ├─ Config/
 │   └─ Libraries/
 ├─ public/
 ├─ writable/
 ├─ card-reader-service/
 ├─ database.sql
 ├─ .env.example
 └─ README.md
```

### 3) Database Schema (ER Summary)

- `users (1) -> (N) patients.created_by / updated_by`
- `users (1) -> (N) visits.created_by / updated_by`
- `patients (1) -> (N) visits`
- `users (1) -> (N) audit_logs`

ตารางหลัก: `users`, `patients`, `visits`, `audit_logs`

### 4) Data Flow: เสียบบัตร -> ตรวจ -> เก็บข้อมูล

1. เจ้าหน้าที่เสียบบัตรประชาชนและกดอ่านข้อมูล
2. CI4 เรียก `POST /api/card/read` ไปยัง Local Card Service
3. ระบบเติมข้อมูลผู้ป่วยอัตโนมัติ หรือกรอกมือได้
4. บันทึกผู้ป่วยใน `patients`
5. แพทย์บันทึกการตรวจใน `visits`
6. ระบบบันทึก audit ใน `audit_logs`

### 5) Security + PDPA Design

- Session-based authentication + role filter (`admin`, `staff`, `doctor`)
- Password hash (`password_hash`)
- CSRF protection
- Audit log สำหรับ login/logout/create/update/delete
- จำกัดสิทธิ์แก้ไข visit เฉพาะ `doctor` และ `admin`
- รองรับ field ความยินยอม `pdpa_consent_at`

## Phase 2: Database & Authentication

- SQL script: `database.sql`
- Models: `app/Models/*.php`
- Login/Logout: `app/Controllers/AuthController.php`
- Auth filters: `app/Filters/AuthFilter.php`, `app/Filters/RoleFilter.php`, `app/Filters/GuestFilter.php`

## Phase 3: Patient & Card Reader

- Patient CRUD + Search: `app/Controllers/PatientController.php`, `app/Views/patients/*`
- หน้าอ่านบัตร: `app/Views/patients/card.php`
- API integration: `app/Controllers/Api/CardController.php`
- Local service mock: `card-reader-service/mock-card-service.js`
- Manual input fallback: กรอกมือได้หาก service ไม่พร้อม

## Phase 4: Visit & Medical Record

- Visit CRUD: `app/Controllers/VisitController.php`
- ฟอร์มตรวจ: `app/Views/visits/form.php`
- Timeline: `app/Views/visits/timeline.php`
- Audit logger: `app/Libraries/AuditLogger.php`

## Phase 5: Report, Export & Deployment

- รายงานประจำวัน + เลือกช่วงวันที่: `app/Controllers/ReportController.php`, `app/Views/reports/daily.php`
- Export Excel: PhpSpreadsheet
- Export PDF: Dompdf
- Environment template: `.env.example`

## Installation

1. สร้างโปรเจกต์ CI4
   - `composer create-project codeigniter4/appstarter clinic-ci4`
2. วางไฟล์จากโปรเจกต์นี้ทับในโฟลเดอร์ CI4
3. คัดลอก env
   - `copy .env.example .env`
4. สร้างฐานข้อมูลและ import SQL
   - `mysql -u root -p < database.sql`
5. ติดตั้ง package export
   - `composer require phpoffice/phpspreadsheet dompdf/dompdf`
6. รัน card reader service
   - `cd card-reader-service`
   - `npm install`
   - `npm run start`
7. รันระบบ
   - `php spark serve --host 0.0.0.0 --port 8080`

## Deployment Checklist

- [ ] ตั้ง `CI_ENVIRONMENT=production`
- [ ] ใช้ HTTPS
- [ ] ตั้งค่า session/cookie ให้ปลอดภัย
- [ ] จำกัดสิทธิ์โฟลเดอร์ `writable/`
- [ ] เปลี่ยนรหัสผ่านเริ่มต้นทุกบัญชี
- [ ] จำกัดการเข้าถึง DB ด้วย firewall
- [ ] เปิด backup และ log rotation
- [ ] ทดสอบ role permission และ CSRF

## Default Accounts

- `admin / admin123`
- `doctor1 / admin123`
- `staff1 / admin123`
