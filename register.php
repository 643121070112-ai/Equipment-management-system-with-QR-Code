<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (strlen($password) < 6) {
        echo "<script>alert('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'); window.history.back();</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $check_query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($check_query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น'); window.history.back();</script>";
        exit();
    } 


    $insert_query = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด กรุณาลองใหม่'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิกแอดมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>

        body {
            background: linear-gradient(135deg, #42a5f5, #7e57c2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .btn-custom {
            border-radius: 25px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="register-container text-center">
    <h2 class="mb-4"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิก</h2>
    <form action="register.php" method="POST">
        <div class="mb-3 text-start">
            <label class="form-label"><i class="fa-solid fa-user"></i> ชื่อผู้ใช้:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label"><i class="fa-solid fa-lock"></i> รหัสผ่าน:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success btn-custom w-100"><i class="fa-solid fa-user-check"></i> สมัครสมาชิก</button>
    </form>

    <div class="mt-3">
        <p>มีบัญชีอยู่แล้ว? <a href="login.php" class="btn btn-primary btn-custom">เข้าสู่ระบบ</a></p>
    </div>

    <div class="mt-3">
        <a href="index.php" class="btn btn-secondary btn-custom"><i class="fa-solid fa-arrow-left"></i> กลับไปหน้าแรก</a>
    </div>
</div>

</body>
</html>
