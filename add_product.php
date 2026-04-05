<?php
session_start();
include 'config.php';
require_once __DIR__ . '/libs/phpqrcode/qrlib.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];
    $quantity = intval($_POST['quantity']);
    $location = $_POST['location'];
    $status = $_POST['status'];

    $upload_dir = "uploads/";
    $qr_dir = "qrcodes/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);


    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE product_code = ?");
    $stmt->execute([$product_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        echo "<script>alert('รหัสครุภัณฑ์นี้ถูกใช้แล้ว กรุณากรอกรหัสใหม่');</script>";
    } else {
        if (!empty($_FILES["product_image"]["name"])) {
            $allowed_ext = ['jpg', 'jpeg', 'png'];
            $image_ext = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));

            if (!in_array($image_ext, $allowed_ext)) {
                echo "<script>alert('❌ อัปโหลดได้เฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น');</script>";
                exit();
            }

            $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
            $target_file = $upload_dir . $image_name;

            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                for ($i = 1; $i <= $quantity; $i++) {
                    $unique_product_code = $product_code . "-" . uniqid(); 

                    $stmt = $conn->prepare("INSERT INTO products (product_code, name, location, image, status, updated_at) 
                                            VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$unique_product_code, $name, $location, $target_file, $status]);

                    $product_id = $conn->lastInsertId();
                    $qr_code_path = $qr_dir . "product_" . $product_id . ".png";
                    $qr_data = "http://c6c60bdeb4fb.sn.mynetname.net/qrkaru/product_detail.php?id=" . urlencode($product_id);

                    QRcode::png($qr_data, $qr_code_path, QR_ECLEVEL_L, 4);

                    $update_stmt = $conn->prepare("UPDATE products SET qr_code = ? WHERE id = ?");
                    $update_stmt->execute([$qr_code_path, $product_id]);
                }

                echo "<script>alert('✅ เพิ่มสินค้าสำเร็จ!'); window.location.href='dashboard.php';</script>";
                exit();
            } else {
                echo "<script>alert('❌ ไม่สามารถอัปโหลดรูปภาพได้');</script>";
            }
        } else {
            echo "<script>alert('❌ กรุณาอัปโหลดรูปภาพสินค้า');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-success {
            width: 100%;
            font-size: 18px;
            background: linear-gradient(to right, #28a745, #218838);
            border: none;
            transition: 0.3s;
            border-radius: 10px;
        }
        .btn-success:hover {
            background: linear-gradient(to right, #218838, #1e7e34);
        }
        .form-label {
            font-weight: bold;
        }
        input[type="file"] {
            padding: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📦 เพิ่มครุภัณฑ์</h2>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">รหัสครุภัณฑ์:</label>
            <input type="text" name="product_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ชื่อครุภัณฑ์:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">จำนวน:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">สถานที่เก็บ:</label>
            <select name="location" class="form-control" required>
                <option value="">เลือกสถานที่</option>
                <option value="c5001">C5001</option>
                <option value="c5002">C5002</option>
                <option value="c5003">C5003</option>
                <option value="c5004">C5004</option>
                <option value="c5005">C5005</option>
                <option value="c5006">C5006</option>
                <option value="c5007">C5007</option>
                <option value="c5008">C5008</option>
                <option value="c5009">C5009</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">สถานะ:</label>
            <select name="status" class="form-control" required>
                <option value="ใช้งานได้">ใช้งานได้</option>
                <option value="เสียหาย">เสียหาย</option>
                <option value="ไม่มีการใช้งาน">ไม่มีการใช้งาน</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">อัปโหลดรูปครุภัณฑ์:</label>
            <input type="file" name="product_image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">เพิ่มครุภัณฑ์</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>