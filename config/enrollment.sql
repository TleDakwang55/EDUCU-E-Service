-- ลบตาราง enrollments เดิม หากมีอยู่ (ระวัง! ข้อมูลจะหายหมด)
DROP TABLE IF EXISTS enrollments;

-- สร้างตาราง enrollments ใหม่
CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(255) NOT NULL,
    course_code VARCHAR(255) NOT NULL,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_course_code (course_code)
);