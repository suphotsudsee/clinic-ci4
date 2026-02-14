CREATE DATABASE IF NOT EXISTS clinic_ci4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinic_ci4;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    role ENUM('admin', 'staff', 'doctor') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cid VARCHAR(13) NOT NULL UNIQUE,
    hn VARCHAR(20) NOT NULL UNIQUE,
    title_name VARCHAR(20) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('M', 'F', 'O') NULL,
    dob DATE NULL,
    phone VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    allergy_note TEXT NULL,
    pdpa_consent_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_name (first_name, last_name),
    CONSTRAINT fk_patients_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_patients_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    visit_date DATETIME NOT NULL,
    chief_complaint VARCHAR(255) NOT NULL,
    vital_signs VARCHAR(255) NULL,
    diagnosis TEXT NOT NULL,
    diseasecode VARCHAR(7) NULL,
    treatment TEXT NULL,
    medication TEXT NULL,
    doctor_note TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_visit_date (visit_date),
    INDEX idx_patient_visit (patient_id, visit_date),
    INDEX idx_diseasecode (diseasecode),
    CONSTRAINT fk_visits_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    CONSTRAINT fk_visits_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(50) NOT NULL,
    resource_type VARCHAR(50) NOT NULL,
    resource_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    old_data JSON NULL,
    new_data JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_resource (resource_type, resource_id),
    INDEX idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO users (username, password_hash, full_name, role, is_active)
VALUES
('admin', '$2y$10$XH13RVsvERLS74EvNfcGt.OhYfxsxCww33wWD2PMW8GZscFWsY4zS', 'ผู้ดูแลระบบ', 'admin', 1),
('doctor1', '$2y$10$XH13RVsvERLS74EvNfcGt.OhYfxsxCww33wWD2PMW8GZscFWsY4zS', 'นพ.สมชาย', 'doctor', 1),
('staff1', '$2y$10$XH13RVsvERLS74EvNfcGt.OhYfxsxCww33wWD2PMW8GZscFWsY4zS', 'เจ้าหน้าที่เวชระเบียน', 'staff', 1);

-- Password default for seeded users: admin123
