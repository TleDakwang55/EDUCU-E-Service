<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนนิสิตใหม่</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        a {
            text-decoration: none;
            color: #c84a83; /* สีชมพู */
            margin-left: 10px;
        }
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #c84a83;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* เพื่อให้ padding ไม่เพิ่มขนาดของ input */
        }
        button[type="submit"] {
            <font-family: 'Kanit', sans-serif;>
            width: 100%;
            padding: 12px;
            background-color: #c84a83;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #a33b6d; /* สีเข้มขึ้นเมื่อโฮเวอร์ */
        }
        /* เพิ่มเติม: จัดการข้อความแจ้งเตือน (ถ้ามี) */
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<?php
include('../config/db.php'); // ไฟล์นี้ต้องสร้างตัวแปร $conn สำหรับการเชื่อมต่อฐานข้อมูล

// รับค่าจากฟอร์ม
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$thaiid = $_POST['thaiid'] ?? '';
$error_message = '';

// ตรวจสอบว่ากรอกครบและทำการประมวลผลเมื่อเป็น POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($first_name && $last_name && $thaiid) {
        // เพิ่มข้อมูลนิสิต
        $sql = "INSERT INTO students (first_name, last_name, thaiid) VALUES (?, ?, ?)";
        $stmt = null; // กำหนดค่าเริ่มต้นเป็น null

        try {
            // เตรียมคำสั่ง SQL
            $stmt = $conn->prepare($sql); // ใช้ $conn
            if ($stmt === false) {
                 throw new Exception("เตรียมคำสั่ง SQL ไม่สำเร็จ: " . $conn->error);
            }
            $stmt->bind_param("sss", $first_name, $last_name, $thaiid);

            // พยายาม実行 (execute) คำสั่ง
            $stmt->execute(); // บรรทัดนี้อาจโยน Exception หากเกิดข้อผิดพลาด

            // การ INSERT สำเร็จ
            $last_id = $conn->insert_id;

            // สร้างรหัสนิสิต
            $student_code = "674" . str_pad($last_id, 5, '0', STR_PAD_LEFT) . "27";

            // อัปเดต student_code
            $update_sql = "UPDATE students SET student_code = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql); // ใช้ $conn
             if ($update_stmt === false) {
                 throw new Exception("เตรียมคำสั่ง UPDATE SQL ไม่สำเร็จ: " . $conn->error);
            }
            $update_stmt->bind_param("si", $student_code, $last_id);
            $update_stmt->execute();


            // ส่งไปหน้า set_password พร้อม id
            header("Location: ../Public/set_password.php?id=$last_id");
            exit();

        } catch (mysqli_sql_exception $e) {
            // ดักจับ Exception ที่เกิดจากฐานข้อมูล
            // Error code 1062 คือ Duplicate entry for key
            if ($e->getCode() == 1062) {
                 $error_message = "เลขบัตรประชาชนนี้ได้ถูกใช้ลงทะเบียนแล้ว";
            } else {
                // ข้อผิดพลาดอื่นๆ ที่ไม่ใช่ Duplicate entry
                $error_message = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $e->getMessage();
                 // หรือจะ throw $e; เพื่อดูรายละเอียด error เต็มรูปแบบในระหว่างการพัฒนา
            }
        } catch (Exception $e) {
             // ดักจับ Exception อื่นๆ ที่ไม่ใช่จากฐานข้อมูล (เช่น prepare ล้มเหลว)
             $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
        } finally {
            // ปิด statement ถ้ามีการเตรียมไว้
            if ($stmt !== null) {
                $stmt->close();
            }
             if (isset($update_stmt) && $update_stmt !== null) {
                $update_stmt->close();
            }
        }

    } else {
        // กรอกข้อมูลไม่ครบ
        $error_message = "กรุณากรอกข้อมูลให้ครบ";
    }
}
?>

<div class="container">
    <h1>นิสิตใหม่ลงทะเบียนรับรหัสผ่าน</h1>

    <?php if ($error_message): // แสดงข้อความผิดพลาดถ้ามี ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="register_student.php" method="POST">
        <div class="form-group">
            <label for="f_name">ชื่อ</label>
            <input type="text" name="first_name" id="f_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="l_name">นามสกุล</label>
            <input type="text" name="last_name" id="l_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="thaiid">รหัสบัตรประชาชน (Thai ID)</label>
            <input type="text" name="thaiid" id="thaiid" class="form-control" value="<?php echo htmlspecialchars($thaiid); ?>" required>
        </div>

        <button type="submit" name="add_students">ลงทะเบียนนิสิตใหม่</button>
        <a href="../Public/login.php"class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>

</body>
</html>