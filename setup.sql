-- ============================================================
--  Student Management System — Database Setup
--  Run this in phpMyAdmin (SQL tab) once before starting.
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_management_db;
USE student_management_db;

-- ── Departments ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Departments (
    dept_id   INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Teachers ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Teachers (
    teacher_id   INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(100) UNIQUE,
    designation  VARCHAR(80),
    dept_id      INT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES Departments(dept_id) ON DELETE SET NULL
);

-- ── Courses ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Courses (
    course_id   INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(120) NOT NULL,
    credit_hour DECIMAL(3,1) DEFAULT 3.0,
    dept_id     INT,
    teacher_id  INT,
    FOREIGN KEY (dept_id)    REFERENCES Departments(dept_id)  ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES Teachers(teacher_id)  ON DELETE SET NULL
);

-- ── Students ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Students (
    student_id VARCHAR(20)  PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) UNIQUE,
    phone      VARCHAR(20),
    dept_id    INT,
    semester   TINYINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES Departments(dept_id) ON DELETE SET NULL
);

-- ── Enrollments ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id    VARCHAR(20),
    course_id     INT,
    grade         VARCHAR(5)  DEFAULT NULL,
    enrolled_on   DATE        DEFAULT (CURRENT_DATE),
    UNIQUE KEY uq_enroll (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES Courses(course_id)   ON DELETE CASCADE
);

-- ── Sample seed data ─────────────────────────────────────────
INSERT IGNORE INTO Departments (dept_name) VALUES
    ('Computer Science and Engineering'),
    ('Electrical Engineering'),
    ('Business Administration');

INSERT IGNORE INTO Teachers (name, email, designation, dept_id) VALUES
    ('Md. Tohidul Islam', 'tohidul@neub.edu.bd', 'Lecturer', 1),
    ('Dr. Kamal Hossain',  'kamal@neub.edu.bd',  'Assistant Professor', 1),
    ('Ms. Rina Begum',     'rina@neub.edu.bd',   'Lecturer', 2);

INSERT IGNORE INTO Courses (course_code, course_name, credit_hour, dept_id, teacher_id) VALUES
    ('CSE301', 'Database Management System', 3.0, 1, 1),
    ('CSE201', 'Data Structures & Algorithms', 3.0, 1, 2),
    ('EEE101', 'Circuit Theory', 3.0, 2, 3);

INSERT IGNORE INTO Students (student_id, name, email, phone, dept_id, semester) VALUES
    ('05624205101062', 'Hridoy Paul', 'hridoy@student.neub.edu.bd', '01700000001', 1, 4);
