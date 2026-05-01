-- =====================================================
-- Laberion Workforce Management System
-- Database Schema and Sample Data
-- =====================================================

-- Drop existing database if it exists
DROP DATABASE IF EXISTS laberion_wms;

-- Create database
CREATE DATABASE laberion_wms;
USE laberion_wms;

-- =====================================================
-- TABLE: admin_users
-- Description: Administrator user accounts
-- =====================================================
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    full_name VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: workers
-- Description: Employee/Worker information
-- =====================================================
CREATE TABLE workers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    position VARCHAR(100),
    department VARCHAR(100),
    salary DECIMAL(10, 2),
    photo VARCHAR(255),
    face_data LONGTEXT,
    status ENUM('active', 'inactive', 'vacation', 'sick') DEFAULT 'active',
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_lastname (lastname),
    INDEX idx_email (email),
    INDEX idx_department (department),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: attendance
-- Description: Daily attendance records
-- =====================================================
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    hours DECIMAL(4, 2),
    status ENUM('present', 'late', 'absent', 'half-day') DEFAULT 'present',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (worker_id, date),
    INDEX idx_worker_id (worker_id),
    INDEX idx_date (date),
    INDEX idx_status (status),
    INDEX idx_worker_date (worker_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: leaves
-- Description: Leave requests and approvals
-- =====================================================
CREATE TABLE leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL,
    type ENUM('vacation', 'sick', 'personal', 'unpaid') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_worker_id (worker_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_worker_status (worker_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: departments
-- Description: Company departments
-- =====================================================
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    manager_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (manager_id) REFERENCES workers(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: positions
-- Description: Job positions/titles
-- =====================================================
CREATE TABLE positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    department_id INT,
    salary_min DECIMAL(10, 2),
    salary_max DECIMAL(10, 2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_department_id (department_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: activity_logs
-- Description: System activity and audit logs
-- =====================================================
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    worker_id INT,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE SET NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_worker_id (worker_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: system_settings
-- Description: System configuration settings
-- =====================================================
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: face_encodings
-- Description: Face recognition encodings
-- =====================================================
CREATE TABLE face_encodings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL UNIQUE,
    encoding LONGTEXT NOT NULL,
    image_path VARCHAR(255),
    confidence DECIMAL(3, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    INDEX idx_worker_id (worker_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: notifications
-- Description: System notifications
-- =====================================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    worker_id INT,
    type VARCHAR(50),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    INDEX idx_admin_id (admin_id),
    INDEX idx_worker_id (worker_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

-- Insert Admin Users
INSERT INTO admin_users (username, password, email, full_name, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@laberion.com', 'Administrator', 'active'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@laberion.com', 'Manager', 'active'),
('hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr@laberion.com', 'HR Officer', 'active');

-- Insert Departments
INSERT INTO departments (name, description, status) VALUES
('Information Technology', 'IT Department', 'active'),
('Human Resources', 'HR Department', 'active'),
('Finance', 'Finance Department', 'active'),
('Marketing', 'Marketing Department', 'active'),
('Sales', 'Sales Department', 'active'),
('Operations', 'Operations Department', 'active'),
('Customer Service', 'Customer Service Department', 'active');

-- Insert Positions
INSERT INTO positions (title, description, department_id, salary_min, salary_max, status) VALUES
('Software Engineer', 'Develops and maintains software applications', 1, 50000, 80000, 'active'),
('Senior Developer', 'Senior software development role', 1, 70000, 100000, 'active'),
('DevOps Engineer', 'Infrastructure and deployment', 1, 55000, 85000, 'active'),
('HR Manager', 'Manages HR operations', 2, 45000, 65000, 'active'),
('Accountant', 'Financial accounting', 3, 40000, 60000, 'active'),
('Marketing Manager', 'Marketing strategy and execution', 4, 50000, 75000, 'active'),
('Sales Representative', 'Sales and client relations', 5, 35000, 55000, 'active'),
('Operations Manager', 'Operations management', 6, 45000, 70000, 'active'),
('Customer Support', 'Customer support and service', 7, 30000, 45000, 'active');

-- Insert Workers
INSERT INTO workers (name, lastname, email, phone, position, department, salary, status, start_date) VALUES
('John', 'Doe', 'john.doe@laberion.com', '+1-555-0101', 'Software Engineer', 'Information Technology', 65000, 'active', '2022-01-15'),
('Jane', 'Smith', 'jane.smith@laberion.com', '+1-555-0102', 'Senior Developer', 'Information Technology', 85000, 'active', '2021-06-20'),
('Michael', 'Johnson', 'michael.johnson@laberion.com', '+1-555-0103', 'DevOps Engineer', 'Information Technology', 70000, 'active', '2022-03-10'),
('Sarah', 'Williams', 'sarah.williams@laberion.com', '+1-555-0104', 'HR Manager', 'Human Resources', 55000, 'active', '2020-09-01'),
('Robert', 'Brown', 'robert.brown@laberion.com', '+1-555-0105', 'Accountant', 'Finance', 50000, 'active', '2021-11-15'),
('Emily', 'Davis', 'emily.davis@laberion.com', '+1-555-0106', 'Marketing Manager', 'Marketing', 60000, 'active', '2022-02-01'),
('David', 'Miller', 'david.miller@laberion.com', '+1-555-0107', 'Sales Representative', 'Sales', 45000, 'active', '2023-01-10'),
('Lisa', 'Wilson', 'lisa.wilson@laberion.com', '+1-555-0108', 'Operations Manager', 'Operations', 58000, 'active', '2021-08-15'),
('James', 'Moore', 'james.moore@laberion.com', '+1-555-0109', 'Customer Support', 'Customer Service', 38000, 'active', '2023-03-01'),
('Patricia', 'Taylor', 'patricia.taylor@laberion.com', '+1-555-0110', 'Software Engineer', 'Information Technology', 62000, 'active', '2022-07-15');

-- Insert Sample Attendance Records (Last 30 days)
INSERT INTO attendance (worker_id, date, check_in, check_out, hours, status) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:30:00', 8.5, 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 28 DAY), '09:15:00', '17:45:00', 8.5, 'late'),
(1, DATE_SUB(CURDATE(), INTERVAL 27 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 26 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 25 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '08:45:00', '17:15:00', 8.5, 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 28 DAY), '09:00:00', '17:30:00', 8.5, 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 27 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:30:00', '17:45:00', 8.25, 'late'),
(3, DATE_SUB(CURDATE(), INTERVAL 28 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 28 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 28 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(9, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present'),
(10, DATE_SUB(CURDATE(), INTERVAL 29 DAY), '09:00:00', '17:00:00', 8.0, 'present');

-- Insert Sample Leave Requests
INSERT INTO leaves (worker_id, type, start_date, end_date, reason, status, approved_by) VALUES
(1, 'vacation', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Summer vacation', 'approved', 1),
(2, 'sick', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Flu', 'approved', 1),
(3, 'personal', DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Personal appointment', 'pending', NULL),
(4, 'vacation', DATE_ADD(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'Family trip', 'approved', 1),
(5, 'sick', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Medical treatment', 'approved', 1),
(6, 'vacation', DATE_ADD(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 37 DAY), 'Holiday', 'pending', NULL),
(7, 'personal', DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Doctor appointment', 'approved', 1),
(8, 'vacation', DATE_ADD(CURDATE(), INTERVAL 21 DAY), DATE_ADD(CURDATE(), INTERVAL 28 DAY), 'Vacation', 'approved', 1),
(9, 'sick', DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Cold', 'approved', 1),
(10, 'personal', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Personal matter', 'pending', NULL);

-- Insert System Settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('app_name', 'Laberion WMS', 'string', 'Application name'),
('app_version', '1.0.0', 'string', 'Application version'),
('work_start_time', '09:00:00', 'string', 'Work start time'),
('work_end_time', '17:00:00', 'string', 'Work end time'),
('late_threshold', '15', 'integer', 'Late threshold in minutes'),
('timezone', 'Europe/Zurich', 'string', 'System timezone'),
('date_format', 'Y-m-d', 'string', 'Date format'),
('time_format', 'H:i:s', 'string', 'Time format'),
('enable_face_recognition', 'true', 'boolean', 'Enable face recognition'),
('face_recognition_threshold', '0.6', 'string', 'Face recognition confidence threshold'),
('max_login_attempts', '5', 'integer', 'Maximum login attempts'),
('session_timeout', '1800', 'integer', 'Session timeout in seconds'),
('items_per_page', '15', 'integer', 'Items per page in lists'),
('enable_email_notifications', 'false', 'boolean', 'Enable email notifications'),
('smtp_host', 'localhost', 'string', 'SMTP host'),
('smtp_port', '25', 'integer', 'SMTP port');

-- =====================================================
-- VIEWS
-- =====================================================

-- View: Today's Attendance Summary
CREATE VIEW v_today_attendance AS
SELECT 
    w.id,
    w.name,
    w.lastname,
    w.position,
    w.department,
    a.check_in,
    a.check_out,
    a.status,
    a.hours
FROM workers w
LEFT JOIN attendance a ON w.id = a.worker_id AND a.date = CURDATE()
WHERE w.status = 'active'
ORDER BY w.name;

-- View: Monthly Attendance Summary
CREATE VIEW v_monthly_attendance AS
SELECT 
    w.id,
    w.name,
    w.lastname,
    w.department,
    COUNT(CASE WHEN a.status != 'absent' THEN 1 END) as present_days,
    COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_days,
    COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_days,
    SUM(a.hours) as total_hours,
    MONTH(a.date) as month,
    YEAR(a.date) as year
FROM workers w
LEFT JOIN attendance a ON w.id = a.worker_id
WHERE w.status = 'active'
GROUP BY w.id, MONTH(a.date), YEAR(a.date);

-- View: Active Leaves
CREATE VIEW v_active_leaves AS
SELECT 
    l.id,
    l.worker_id,
    w.name,
    w.lastname,
    w.department,
    l.type,
    l.start_date,
    l.end_date,
    l.status,
    DATEDIFF(l.end_date, l.start_date) + 1 as days
FROM leaves l
JOIN workers w ON l.worker_id = w.id
WHERE l.status = 'approved' 
AND CURDATE() BETWEEN l.start_date AND l.end_date;

-- View: Worker Statistics
CREATE VIEW v_worker_statistics AS
SELECT 
    w.id,
    w.name,
    w.lastname,
    w.position,
    w.department,
    COUNT(DISTINCT a.id) as total_attendance_records,
    COUNT(DISTINCT CASE WHEN a.status = 'present' THEN a.id END) as present_count,
    COUNT(DISTINCT CASE WHEN a.status = 'late' THEN a.id END) as late_count,
    COUNT(DISTINCT CASE WHEN a.status = 'absent' THEN a.id END) as absent_count,
    ROUND(SUM(a.hours), 2) as total_hours,
    ROUND(AVG(a.hours), 2) as avg_hours
FROM workers w
LEFT JOIN attendance a ON w.id = a.worker_id
WHERE w.status = 'active'
GROUP BY w.id;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional indexes for common queries
CREATE INDEX idx_attendance_worker_date ON attendance(worker_id, date);
CREATE INDEX idx_leaves_worker_dates ON leaves(worker_id, start_date, end_date);
CREATE INDEX idx_activity_logs_timestamp ON activity_logs(created_at);
CREATE INDEX idx_workers_department ON workers(department);
CREATE INDEX idx_workers_status ON workers(status);

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Update worker updated_at on attendance insert
DELIMITER $$
CREATE TRIGGER tr_attendance_insert_update_worker
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    UPDATE workers SET updated_at = NOW() WHERE id = NEW.worker_id;
END$$
DELIMITER ;

-- Trigger: Update worker updated_at on leave insert
DELIMITER $$
CREATE TRIGGER tr_leaves_insert_update_worker
AFTER INSERT ON leaves
FOR EACH ROW
BEGIN
    UPDATE workers SET updated_at = NOW() WHERE id = NEW.worker_id;
END$$
DELIMITER ;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure: Get Worker Attendance Summary
DELIMITER $$
CREATE PROCEDURE sp_get_worker_attendance_summary(
    IN p_worker_id INT,
    IN p_month INT,
    IN p_year INT
)
BEGIN
    SELECT 
        w.id,
        w.name,
        w.lastname,
        w.position,
        COUNT(CASE WHEN a.status != 'absent' THEN 1 END) as present_days,
        COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_days,
        COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_days,
        SUM(a.hours) as total_hours,
        ROUND(AVG(a.hours), 2) as avg_hours
    FROM workers w
    LEFT JOIN attendance a ON w.id = a.worker_id
    WHERE w.id = p_worker_id
    AND MONTH(a.date) = p_month
    AND YEAR(a.date) = p_year
    GROUP BY w.id;
END$$
DELIMITER ;

-- Procedure: Get Department Statistics
DELIMITER $$
CREATE PROCEDURE sp_get_department_statistics(
    IN p_department VARCHAR(100)
)
BEGIN
    SELECT 
        w.department,
        COUNT(w.id) as total_workers,
        COUNT(CASE WHEN w.status = 'active' THEN 1 END) as active_workers,
        COUNT(CASE WHEN w.status = 'inactive' THEN 1 END) as inactive_workers,
        ROUND(AVG(w.salary), 2) as avg_salary,
        MIN(w.salary) as min_salary,
        MAX(w.salary) as max_salary
    FROM workers w
    WHERE w.department = p_department
    GROUP BY w.department;
END$$
DELIMITER ;

-- =====================================================
-- GRANTS (Optional - for security)
-- =====================================================

-- Create application user (optional)
-- CREATE USER 'laberion_app'@'localhost' IDENTIFIED BY 'secure_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON laberion_wms.* TO 'laberion_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- END OF DATABASE SCHEMA
-- =====================================================