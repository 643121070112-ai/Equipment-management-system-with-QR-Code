<?php
session_start();
include 'config.php';


if (!isset($_GET['id'])) {
    echo "<script>alert('ไม่พบสินค้าที่ต้องการดูรายละเอียด'); window.location.href = 'index.php';</script>";
    exit();
}

$product_id = intval($_GET['id']); 


$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindValue(1, $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch();

if (!$product) {
    echo "<script>alert('ไม่พบสินค้านี้'); window.location.href = 'index.php';</script>";
    exit();
}


$imagePath = !empty($product['image']) && file_exists($product['image']) ? htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') : "uploads/default.jpg";


$updated_at = !empty($product['updated_at']) ? date("d M Y, H:i น.", strtotime($product['updated_at'])) : "ไม่ระบุ";
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #667eea, #764ba2);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .product-card {
            max-width: 650px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
        }
        .product-card h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            color: #5a67d8;
            margin-bottom: 15px;
        }
        .product-card img {
            display: block;
            margin: 0 auto;
            border-radius: 10px;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        .product-info {
            margin-top: 20px;
        }
        .product-info th {
            width: 40%;
            background: #5a67d8;
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
        }
        .product-info td {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 10px;
            font-weight: 500;
        }
        .btn-container {
            text-align: center;
            margin-top: 25px;
        }
        .btn-custom {
            background-color: #5a67d8;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #434190;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="product-card">
        <h2>📦 รายละเอียดครุภัณฑ์</h2>
        <img src="<?= $imagePath; ?>" alt="รูปภาพสินค้า" class="img-fluid" style="max-width: 300px; border-radius: 10px;">
        <table class="table product-info mt-3">
            <tr>
                <th>📌 ชื่อ</th>
                <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>🔖 รหัสครุภัณฑ์</th>
                <td><?= htmlspecialchars($product['product_code'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>📍 สถานที่เก็บ</th>
                <td><?= htmlspecialchars($product['location'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>🔄 สถานะ</th>
                <td><?= htmlspecialchars($product['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>🕒 อัปเดตล่าสุด</th>
                <td><?= $updated_at; ?></td>
            </tr>
        </table>
        <div class="btn-container">
            <a href="index.php" class="btn btn-custom">⬅️ กลับไปหน้าหลัก</a>
            <?php if (isset($_SESSION['admin'])) { ?>
                <a href="dashboard.php" class="btn btn-secondary">🔧 ไปที่ Dashboard</a>
            <?php } ?>
        </div>
    </div>
</body>
</html>