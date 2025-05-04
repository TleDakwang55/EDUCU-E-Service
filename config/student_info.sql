CREATE TABLE student_info (
    student_code VARCHAR(20) PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    faculty_id INT,
    major_id INT,
    year INT,
    date_of_birth DATE,
    address TEXT,
    profile_picture VARCHAR(255),
    FOREIGN KEY (student_code) REFERENCES students(student_code)
);
