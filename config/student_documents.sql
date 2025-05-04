CREATE TABLE students_documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(10) NOT NULL,  -- รหัสนิสิต (foreign key จากตาราง student_info)
    id_card_path VARCHAR(255) NOT NULL, -- path ไปยังไฟล์สำเนาบัตรประชาชน
    student_image_path VARCHAR(255),    -- path ไปยังไฟล์รูปนิสิต
    change_name_path VARCHAR(255),       -- path ไปยังไฟล์สำเนาใบเปลี่ยนชื่อ (ถ้ามี)
    parent_guarantee_path VARCHAR(255) NOT NULL, -- path ไปยังไฟล์หนังสือรับรองและค้ำประกันจากผู้ปกครอง
    consent_agreement_path VARCHAR(255) NOT NULL, -- path ไปยังไฟล์หนังสือยินยอมเปิดเผยข้อมูล
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- วันที่อัปโหลดเอกสาร
    FOREIGN KEY (student_code) REFERENCES student_info(student_code)
);
