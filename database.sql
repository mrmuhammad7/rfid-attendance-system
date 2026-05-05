-- ============================================
-- RFID Attendance System - Clean Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS attendance_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE attendance_db;

-- ============================================
-- Admins Table
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    mail       VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Students Table
-- ============================================
CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    uid         VARCHAR(20) NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    student_id  VARCHAR(50) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Attendance Table
-- ============================================
CREATE TABLE IF NOT EXISTS attendance (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    student_id  INT NOT NULL,
    date        DATE NOT NULL,
    status      ENUM('present','absent') DEFAULT 'present',
    scanned_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (student_id, date)
);

-- ============================================
-- Sample Admin
-- ============================================
INSERT IGNORE INTO admins (mail, password) VALUES
('admin@admin.com', '$2y$10$JR/AUUWhwarPUWaPxnnSD.uWJ7E15xcFJEFuc.FYtRsn/.XTRYYEW');

-- ============================================
-- Sample Students
-- ============================================
INSERT IGNORE INTO students (uid, name, student_id) VALUES
('6377CAD9', 'Mohamed Mahmmoud', '23/121540'),
('BB1B3402', 'Mazen Mohammed',  '23/121534'),
('43715B06', 'Yassin Saeed',    '23/121256'),
('99EE6E06', 'Mohammed Bakr',   '23/121810'),
('F9F26E06', 'Youssef Ahmed',   '23/121324');

-- ============================================
-- Sample Attendance
-- ============================================
INSERT IGNORE INTO attendance (student_id, date, status) VALUES
(1, '2026-05-03', 'present'),
(1, '2026-05-04', 'present'),
(2, '2026-05-04', 'present'),
(1, '2026-05-05', 'present'),
(2, '2026-05-05', 'present'),
(4, '2026-05-05', 'present'),
(5, '2026-05-05', 'present');