<?php
include ('../config/db.php');

if (!isset($conn)) {
    die("Database connection error.");
}
if ($conn) {
    echo "Database connected successfully.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE staff_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $users = $result->fetch_assoc();
        if (password_verify($password, $users['password'])) {
            session_start(); // **ตรวจสอบให้แน่ใจว่า session_start() อยู่ที่นี่**
            $_SESSION = $staff_id; // เปลี่ยนเป็น 'student_code'
            $_POST[$name] = $name; // เก็บ password ด้วย
            if ($users['role'] == 'Admin') {
                header("Location: ../admin/admin_dashboard.php?staff_id=$staff_id"); // Redirect to admin dashboard
            } elseif ($users['role'] == 'Teachers') {
                header("Location: ../admin/teacher_dashboard.php"); // Redirect to user dashboard if not admin
            }
            exit();
        } else {
            echo "Incorrect password";
            header("Location: ../admin/admin-login.php?error=Incorrect password");
        }
    } else {
        echo "User not found";
    }
}
?>