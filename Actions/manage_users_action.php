<?php
// Start the session
session_start();

// Include the database configuration file
include '../config/db.php'; // Adjust path as needed

// Include the functions file (if needed, though not strictly necessary for a simple delete)
include '../include/functions.php'; // Uncomment if you need helper functions here

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if the 'action' parameter is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Get database connection
        $conn = connectDB(); // Assuming connectDB() function is available and returns connection

        // Check if database connection is successful
        if (!$conn) {
            $_SESSION['error_message'] = "ไม่สามารถเชื่อมต่อฐานข้อมูลเพื่อดำเนินการได้";
            // Redirect back to the manage users page
            // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
            header("Location: ../admin/manage_users.php");
            exit();
        }

        switch ($action) {
            // case 'add':
            //     // --- Handle Add User Action (for students) ---
            //     // This part will be implemented when you add a form for adding students
            //     // You will get student data from the form and insert into the 'students' table.
            //     // Remember to hash the password if you are managing passwords here.
            //     $_SESSION['error_message'] = "ฟังก์ชันเพิ่มผู้ใช้ (นิสิต) ยังไม่ได้ถูกนำไปใช้"; // Placeholder message
            //     break;

            // case 'edit':
            //     // --- Handle Edit User Action (for students) ---
            //     // This part will be implemented when you create the edit student page (edit_student.php).
            //     // You will get the student ID and updated data from the edit form.
            //     // Remember to handle password updates carefully (e.g., update only if a new password is provided).
            //     $_SESSION['error_message'] = "ฟังก์ชันแก้ไขผู้ใช้ (นิสิต) ยังไม่ได้ถูกนำไปใช้"; // Placeholder message
            //     break;

            case 'delete':
                // --- Handle Delete User Action (for students) ---
                // Get the student ID from the form (from manage_users.php table)
                $student_id = $_POST['student_id'] ?? null; // Name from the hidden input in the delete form

                // Basic validation
                if ($student_id === null || !is_numeric($student_id) || $student_id <= 0) {
                    $_SESSION['error_message'] = "ไม่พบรหัสผู้ใช้ (นิสิต) ที่ต้องการลบ หรือรหัสไม่ถูกต้อง";
                } else {
                    // Prepare SQL query to delete a student from the 'students' table
                    // Ensure the WHERE clause uses the correct ID column (assuming 'id')
                    $delete_query = "DELETE FROM students WHERE id = ?"; // Delete based on 'id' column

                    // Prepare the statement
                    if ($stmt = $conn->prepare($delete_query)) {
                        // Bind parameter (assuming student ID is integer)
                        $stmt->bind_param("i", $student_id);

                        // Execute the statement
                        if ($stmt->execute()) {
                            // Check if any row was actually deleted
                            if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "ลบผู้ใช้ (นิสิต) สำเร็จแล้ว";
                            } else {
                                // This might mean the ID wasn't found (already deleted?)
                                $_SESSION['error_message'] = "ไม่พบผู้ใช้ (นิสิต) ที่ต้องการลบ (อาจถูกลบไปแล้ว)";
                            }
                        } else {
                            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบผู้ใช้ (นิสิต): " . $stmt->error;
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล (ลบผู้ใช้ นิสิต): " . $conn->error;
                    }
                }
                break;

            default:
                // Handle unknown action
                $_SESSION['error_message'] = "การดำเนินการไม่ถูกต้อง";
                break;
        }

        // Close database connection after all operations are done
        if (isset($conn) && $conn) {
            $conn->close();
        }

    } else {
        // 'action' parameter is not set
        $_SESSION['error_message'] = "ไม่ระบุการดำเนินการ";
    }

    // Redirect back to the manage users page after processing
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_users.php");
    exit();

} else {
    // If the request method is not POST, redirect to the manage users page
    // or an error page
    // *** ตรวจสอบพาธนี้ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ ***
    header("Location: ../admin/manage_users.php");
    exit();
}
?>
