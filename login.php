<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);


    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $username; 


        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_name, login_time) VALUES (:username, NOW())");
        $log_stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $log_stmt->execute();

        header("Location: dashboard.php"); 
        exit();
    } else {
        echo "<script>alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>

        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
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

<div class="login-container text-center">
    <h2 class="mb-4"><i class="fa-solid fa-user-lock"></i> เข้าสู่ระบบ</h2>
    <form action="login.php" method="POST">
        <div class="mb-3 text-start">
            <label class="form-label"><i class="fa-solid fa-user"></i> ชื่อผู้ใช้:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label"><i class="fa-solid fa-lock"></i> รหัสผ่าน:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-custom w-100"><i class="fa-solid fa-sign-in-alt"></i> เข้าสู่ระบบ</button>
    </form>

    <div class="mt-3">
        <p>ยังไม่มีบัญชี? <a href="register.php" class="btn btn-success btn-custom">สมัครสมาชิก</a></p>
    </div>

    <div class="mt-3">
        <a href="index.php" class="btn btn-secondary btn-custom"><i class="fa-solid fa-arrow-left"></i> กลับไปหน้าแรก</a>
    </div>
</div>

</body>
</html>
