<?php
session_start();
if (!isset($_SESSION['student_code'])) {
    header("Location: login.php");
    exit();
}

include('../include/functions.php');

// Fetch available courses for registration (may include additional conditions such as semester)
$available_courses = getAvailableCoursesForRegistration(); // New function in functions.php

// Fetch current registration data of the student (if any)
$registered_courses = getRegisteredCourses($_SESSION['student_code']); // New function in functions.php

// Process course registration (if the form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register_course'])) {
        $course_id_to_register = $_POST['register_course'];
        $registration_result = registerCourse($_SESSION['student_code'], $course_id_to_register); // New function in functions.php
        if ($registration_result === true) {
            $registration_message = "<p style='color: green;'>ลงทะเบียนเรียนสำเร็จ</p>";
            $registered_courses = getRegisteredCourses($_SESSION['student_code']); // Update the list of registered courses
        } else {
            $registration_message = "<p style='color: red;'>เกิดข้อผิดพลาดในการลงทะเบียนเรียน: {$registration_result}</p>";
        }
    }

    if (isset($_POST['unregister_course'])) {
        $course_id_to_unregister = $_POST['unregister_course'];
        $unregister_result = unregisterCourse($_SESSION['student_code'], $course_id_to_unregister); // New function in functions.php
        if ($unregister_result === true) {
            $unregister_message = "<p style='color: green;'>ยกเลิกรายวิชาเรียบร้อยแล้ว</p>";
            $registered_courses = getRegisteredCourses($_SESSION['student_code']); // Update the list of registered courses
        } else {
            $unregister_message = "<p style='color: red;'>เกิดข้อผิดพลาดในการยกเลิกรายวิชา: {$unregister_result}</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนเรียน - ระบบ e-Service มหาวิทยาลัย</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        title {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8; /* สีพื้นหลังอ่อนๆ */
        }
        button {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8; /* สีพื้นหลังอ่อนๆ */
        }
        .search-bar {
            padding: 10px;
            border: 1px solid #DE5C8E;
            border-radius: 25px; /* ทำให้กลมมน */
            width: 500px;
            font-size: 1em;
            font-family: 'Kanit';/* ใช้ Font Kanit */
            transition: box-shadow 0.3s ease;
            outline: none;
        }
        .search-bar:focus {
            box-shadow: 0 0 5px rgba(222, 92, 142, 0.5); /* เพิ่มเงาเมื่อ Focus */
        }
        .search-bar2 {
            font-family: 'Kanit';/* ใช้ Font Kanit */
            border-radius: 5px;
            size: 100%;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff; /* สีขาว */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #DE5C8E; /* สีชมพู */
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .available-courses h2, .registered-courses h3 {
            color: #DE5C8E; /* สีชมพู */
            margin-bottom: 15px;
            font-size: 1.8em;
            border-bottom: 2px solid #DE5C8E;
            padding-bottom: 10px;
        }
        .course-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .course-item {
            background-color: #ffffff; /* สีขาว */
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 45%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .course-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .course-code {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        .course-info {
            font-size: 1.1em;
            color: #555;
        }
        .register-button, .unregister-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .register-button {
            background-color: #DE5C8E; /* สีชมพู */
            color: #ffffff; /* สีขาว */
        }
        .register-button:hover {
            background-color: #C53E77; /* สีชมพูเข้มขึ้น */
        }
        .unregister-button {
            background-color: #ffffff; /* สีขาว */
            color: #DE5C8E; /* สีชมพู */
            border: 1px solid #DE5C8E;
        }
        .unregister-button:hover {
            background-color: #f0f0f0; /* สีเทาอ่อน */
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
            font-size: 1.1em;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .registered-courses ul {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .registered-courses ul li {
            background-color: #ffffff; /* สีขาว */
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            font-size: 1.1em;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .registered-courses ul li:nth-child(odd) {
            background-color: #f8f8f8; /* สีเทาอ่อน */
        }
        a {
            color: #DE5C8E;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 1.1em;
            margin-top: 15px;
            display: inline-block;
        }
        a:hover {
            color: #C53E77;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            .course-item {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
            }
            .course-item button {
                margin-top: 10px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ลงทะเบียนเรียน</h1>
        <div class="search-bar">
            <label for="search_keyword">ค้นหารหัสวิชา:</label>
            <input type="text" id="search_keyword" name="search_keyword" class="search-bar2" placeholder="กรอกรหัสวิชาที่ต้องการค้นหา">
            <button class="search-bar2" >ค้นหา</button>
            </div>

        <h2>รายวิชาที่เปิดให้ลงทะเบียน</h2>
        <div class="course-list">
        <?php if (!empty($available_courses)): ?>
            <?php foreach ($available_courses as $course): ?>
                <div class="course-item">
                    <div class="course-details">
                        <strong><?php echo $course['course_code']; ?></strong> - <?php echo $course['course_name']; ?> (<?php echo $course['credit']; ?> หน่วยกิต)
                        <p>รายละเอียด: <?php echo $course['description']; ?></p>
                        <p>วัน/เวลา: <?php echo $course['DAY']; ?> <?php echo $course['TIME']; ?></p>
                        <p>จำนวนที่นั่ง: <?php echo $course['available_seats']; ?> / <?php echo $course['total_seats']; ?></p>
                    </div>
                    <div class="course-actions">
                        <?php if (!in_array($course['course_code'], array_column($registered_courses, 'course_code'))): ?>
                            <form method="post">
                                <input type="hidden" name="register_course" value="<?php echo $course['course_code']; ?>">
                                <button type="submit" class="register-button">ลงทะเบียน</button>
                            </form>
                        <?php else: ?>
                            <span style="color: green;">ลงทะเบียนแล้ว</span>
                            <form method="post">
                                <input type="hidden" name="unregister_course" value="<?php echo $course['course_code']; ?>">
                                <button type="submit" class="unregister-button">ยกเลิก</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่มีรายวิชาที่เปิดให้ลงทะเบียนในขณะนี้</p>
        <?php endif; ?>
        </div>

        <?php if (isset($registration_message)): ?>
            <div class="alert"><?php echo $registration_message; ?></div>
        <?php endif; ?>

        <?php if (isset($unregistration_message)): ?>
            <div class="alert"><?php echo $unregistration_message; ?></div>
        <?php endif; ?>

        <div class="registered-courses">
            <h3>วิชาที่ลงทะเบียนแล้ว</h3>
            <?php if (!empty($registered_courses)): ?>
                <ul>
                    <?php foreach ($registered_courses as $registered_course): ?>
                        <li><?php echo $registered_course['course_code']; ?> - <?php echo $registered_course['course_name']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>ยังไม่มีวิชาที่ลงทะเบียน</p>
            <?php endif; ?>
        </div>

        <p><a href="dashboard.php">กลับสู่แดชบอร์ด</a></p>
    </div>
</body>
</html>